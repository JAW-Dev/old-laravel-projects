<?php

namespace App\Nova\Metrics;

use App\SiteStats;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Metrics\Partition;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;

class CFWVersion extends Partition
{

    /**
     * Name
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return void
     */
    public function name() {
        return 'CFW Versions';
    }

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $date = \Carbon\Carbon::today()->subDays(7);
        return $this->count($request, SiteStats::orderBy('cfw_version', 'desc')->where('updated_at', '>=', $date), 'cfw_version','cfw_version');
    }

    /**
     * Return a partition result showing the segments of a aggregate.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder|string  $model
     * @param  string  $function
     * @param  string  $column
     * @param  string  $groupBy
     * @return \Laravel\Nova\Metrics\PartitionResult
     */
    protected function aggregate($request, $model, $function, $column, $groupBy)
    {
        $query = $model instanceof Builder ? $model : (new $model)->newQuery();

        $wrappedColumn = $query->getQuery()->getGrammar()->wrap(
            $column = $column ?? $query->getModel()->getQualifiedKeyName()
        );

        $results = $query->select(
            $groupBy, DB::raw("{$function}({$wrappedColumn}) as aggregate")
        )->groupBy($groupBy)->get();

        $new_results = [];
        foreach ($results as $result) {
            for ($i=0; $i < $result->aggregate; $i++) {
                $string = '{"server":"' . $result->cfw_version . '","aggregate":' . $result->aggregate . '}';
                $new_results[] = $result->cfw_version;
            }
        }

        $versions = [];
        foreach ($new_results as $new_results) {
            $versions[$new_results] = (!isset($versions[$new_results])) ? 1 : $versions[$new_results] + 1;
        }

        $updated_results = [];
        foreach( $versions as $key => $value ) {
            $updated_results[] = [$key => $value];
        }

        $return = $this->result($results->mapWithKeys(function ($result) use ($groupBy, $updated_results) {
            $formated_result = $this->formatAggregateResult($result, $groupBy);
            foreach ($updated_results as $updated_result ) {
                if( key($formated_result) === key($updated_result) ) {
                    return $updated_result;
                }
            }
        })->all());

        $values = $return->value;

        arsort($values);

        $return->value = $values;

        return $return;
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
        return 'c-f-w-version';
    }
}
