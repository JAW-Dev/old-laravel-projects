<?php

namespace App\Nova\Metrics;

use App\SiteStats;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ServerType extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, SiteStats::orderBy('aggregate', 'desc'), 'server', 'server');
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
            $match = explode("/", $result->server, 2);
            $result->server = $match[0];

            $array = [
                "server" => $result->server,
                "aggregate" => $result->aggregate
            ];
            $new_results[] = $array;
        }

        $updated_results = [];
        foreach ($new_results as $new_result) {
            if (!isset($updated_results[$new_result['server']])) {
                $updated_results[$new_result['server']] = $new_result['aggregate'];
            } else {
                $updated_results[$new_result['server']] += $new_result['aggregate'];
            }
        }

        return $this->result($results->mapWithKeys(function ($result) use ($groupBy, $updated_results) {
            $formated_result = $this->formatAggregateResult($result, $groupBy);
            foreach ($updated_results as $key => $value ) {
                if( key($formated_result) === $key ) {
                    return [$key => $value];
                }
            }
        })->all());
    }

    /**
     * Name
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return void
     */
    public function name() {
        return 'Server';
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
        return 'server-type';
    }
}
