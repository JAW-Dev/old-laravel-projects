<?php

namespace App\Nova\Metrics;

use App\SiteStats;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Http\Requests\NovaRequest;

class TopShippingMethods extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $collection = with(new SiteStats)
			->all('shipping_methods')
			->each(function($item) {
                $data = $item['shipping_methods'];
                $methods = [];

				foreach($data as $index => $value) {
                    $methods[] = $value['id'];
                }

				$item['shipping_methods'] = $methods;

				return $item;
            })
			->arrayCountValues('shipping_methods')
            ->toArray();

        uasort($collection, function($a, $b) {
            return $this->calculateSort($a, $b);
        });

        // // Limit to 10 results.
        $collection = array_slice($collection, 0, 10);

        return $this->result( $collection );
    }

    /**
	 * Override this if you want to change the sorting behavior
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
    public function calculateSort($a, $b): int {
		if ($a == $b) {
			return 0;
		}

		$test = $a > $b ? -1 : 1;

		return $test;
	}

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'top-shipping-methods';
    }
}
