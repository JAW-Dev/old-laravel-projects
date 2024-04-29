<?php

namespace App\Http\Controllers\API\v1;

use App\SiteStats;
use Carbon\Carbon;
use App\OrderStats;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Helpers\Remote\ExchangeRates;
use Laravel\Nova\Fields\Currency;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{

    /**
     * Thirty day cache key name
     *
     * @var    string The thirty-day cache key
     * @author Jason Witt
     * @since  1.0.0
     */
    protected $thirtyDayCacheName = 'thirty_day_totals';

    /**
     * Yearly cache key name
     *
     * @var    string
     * @author Jason Witt
     * @since  1.0.0
     */
    protected $yearlyCacheName = 'yearly_totals';

    /**
     * Cache Time
     *
     * 24 Hours
     *
     * @var    string
     * @author Jason Witt
     * @since  1.0.0
     */
    protected $cacheTime = '86400';

    /**
     * The OrderStats model
     *
     * @var OrderStats
     */
    private $orderStats;


    /**
     * StatController constructor.
     *
     * @param SiteStats  $siteStats  The model bish
     * @param OrderStats $orderStats The model bish
     */
    public function __construct(SiteStats $siteStats, OrderStats $orderStats)
    {
        $this->siteStats  = $siteStats;
        $this->orderStats = $orderStats;

    }//end __construct()


    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index(): array
    {
        $key    = env('API_KEY', 'Laravel');
        $getKey = isset($_GET['api_key']) === true ? htmlspecialchars($_GET['api_key']) : '';

        if ($getKey === $key) {
            return $this->get_totals();
        }

        return [];

    }//end index()


    /**
     * Get Totals
     *
     * @return array
     * @since  1.0.0
     *
     * @author Jason Witt
     */
    public function get_totals(): array
    {
        Cache::forget($this->thirtyDayCacheName);
        $data          = [];
        $thirtyDayData = Cache::get($this->thirtyDayCacheName);

        if (empty($thirtyDayData)) {
            $thirtyDayData = [
                'total_sales'  => '$'.number_format($this->get_stats('total_sales', 30)),
                'total_orders' => $this->get_stats('total_orders', 30),
                'total_items'  => $this->get_stats('total_items', 30),
                'total_sites'  => $this->thirty_day_sites(),
            ];

            Cache::put($this->thirtyDayCacheName, $thirtyDayData, $this->cacheTime);
        }

        Cache::forget($this->yearlyCacheName);
        $yearlyData = Cache::get($this->yearlyCacheName);

        if (empty($yearlyData)) {
            $yearlyData = [
                'total_sales'  => '$'.number_format($this->get_stats('total_sales', 365)),
                'total_orders' => $this->get_stats('total_orders', 365),
                'total_items'  => $this->get_stats('total_items', 365),
                'total_sites'  => $this->yearly_sites(),
            ];

            Cache::put($this->yearlyCacheName, $yearlyData, $this->cacheTime);
        }

        $orderData = [
            'thirty_day' => $thirtyDayData,
            'yearly'     => $yearlyData,
        ];

        return array_merge($data, $orderData);

    }//end get_totals()


    /**
     * Total Stats
     *
     * @param string $column The column
     *
     * @return float
     * @author Jason Witt
     * @since  1.0.0
     */
    public function total_stats(string $column): float
    {
        return (float) DB::table('order_stats')->sum($column);

    }//end total_stats()


    /**
     * Total Sites
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return int
     */
    public function total_sites(): int
    {
        return DB::table('order_stats')->distinct('site_id')->count('site_id');

    }//end total_sites()


    /**
     * Determines how many days our stat collection item overlaps with our date range
     *
     * @param mixed  $item         The stat collection item
     * @param Carbon $startingDate The starting date of the range
     * @param int    $maxDays      The number of days that overlap the range
     *
     * @return int
     */
    protected function days_in_range($item, Carbon $startingDate, $maxDays=7): int
    {
        $itemDate = Carbon::parse($item->created_at);

        return min($startingDate->diffInDays($itemDate), $maxDays);

    }//end days_in_range()


    /**
     * Get stats for a particular column and number of days
     *
     * @param string $column The column name
     * @param int    $days   The number of days
     *
     * @return float
     */
    public function get_stats(string $column, int $days): float
    {
        $startingDate = Carbon::today()->subDays($days);
        $query        = DB::table('order_stats')->select([$column, 'id', 'created_at', 'site_id', 'total_sales', 'total_sales_localized'])->where('created_at', '>=', $startingDate)->get();
        $grandTotal   = 0;

        foreach ($query as $item) {
            $total = $item->$column;

            if (floatval($total) === 0.0) {
                continue;
            }

            if ($column === 'total_sales') {
                $total = floatval($item->total_sales_localized ?? $this->convert_currency($item, $total));
            }

            if ($startingDate->isBefore(Carbon::create(2021, 11, 3))) {
                $multiplier = ( $this->days_in_range($item, $startingDate, 6) / 6 );
            } else {
                $multiplier = ( $this->days_in_range($item, $startingDate) / 7 );
            }

            $grandTotal += ($total * min($multiplier, 1));
        }//end foreach

        return $grandTotal;

    }//end get_stats()


    /**
     * Thirty Day Sites
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return int
     */
    public function thirty_day_sites(): int
    {
        $date = Carbon::today()->subDays(30);
        return DB::table('order_stats')->select('site_id')->where('created_at', '>=', $date)->distinct()->count('site_id');

    }//end thirty_day_sites()


    /**
     * Yearly Sites
     *
     * @return int
     * @since  1.0.0
     *
     * @author Jason Witt
     */
    public function yearly_sites(): int
    {
        $date = Carbon::today()->subDays(365);
        return DB::table('order_stats')->select('site_id')->where('created_at', '>=', $date)->distinct()->count('site_id');

    }//end yearly_sites()


    /**
     * Convert Currency
     *
     * @param mixed $item   The table entry.
     * @param float $amount The amount to convert.
     *
     * @since 1.0.0
     *
     * @return float
     *
     * @author Jason Witt
     */
    public function convert_currency($item, float $amount): float
    {
        if (empty($item) || $amount === 0.0) {
            return $amount;
        }

        $siteRecords = DB::table('site_stats')->select(['site_id', 'wc_settings'])->where('site_id', '=', $item->site_id)->get();

        if ($siteRecords->isEmpty()) {
            return $amount;
        }

        $siteRecord  = $siteRecords->first();
        $rawSettings = (array) $siteRecord->wc_settings;

        if (empty($rawSettings[0])) {
            return $amount;
        }

        $settings = json_decode($rawSettings[0]);

        $currency  = ! empty($settings->woocommerce_currency) ? $settings->woocommerce_currency : false;
        $newAmount = ! empty($currency) ? ExchangeRates::convert($amount, $currency) : 0.0;

        if ($item->total_sales_localized === null && $newAmount !== 0.0) {
            if ($newAmount == 93.6) {
                $foo = true;
            }

            DB::table('order_stats')->where('site_id', '=', $item->site_id)->where('id', '=', $item->id)->update(['total_sales_localized' => $newAmount]);
        }

        return $newAmount;

    }//end convert_currency()


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request The request
     *
     * @return Response
     */
    public function store(Request $request): Response
    {

        if (version_compare($request->cfw_version, '6.1.2', '>=')) {
            $this->site_stats($request);
            $this->order_stats($request);
        }

        return response(null, 200);

    }//end store()


    /**
     * Create Order Stats
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return void
     */
    public function order_stats(Request $request)
    {
        $this->orderStats->create($request->wc_order_stats);

    }//end order_stats()


    /**
     * Site Stats
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return void
     */
    public function site_stats($request)
    {
        $dateTime = Carbon::parse($request->install_date);
        $request['install_date'] = $dateTime->format('Y-m-d');
        $request['site_id']      = $request['wc_order_stats']['site_id'];

        $siteId = ['site_id' => $request['site_id']];

        SiteStats::updateOrCreate($siteId, $request->except(['wc_order_stats']));

    }//end site_stats()


}//end class
