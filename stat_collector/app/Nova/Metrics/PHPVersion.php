<?php

namespace App\Nova\Metrics;

use App\SiteStats;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PHPVersion extends Partition
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
        return 'PHP Versions';
    }

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, SiteStats::orderBy('aggregate', 'desc'), 'php_version', 'php_version');
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
            $result->php_version = substr($result->php_version, 0, strpos($result->php_version, '.', strpos($result->php_version, '.')+1));
            $string = '{"php_version":"' . $result->php_version . '","aggregate":' . $result->aggregate . '}';

            $array = [
                "php_version" => $result->php_version,
                "aggregate" => $result->aggregate
            ];
            $new_results[] = $array;
        }

        $updated_results = [];
        foreach ($new_results as $new_result) {
            if (!isset($updated_results[$new_result['php_version']])) {
                $updated_results[$new_result['php_version']] = $new_result['aggregate'];
            } else {
                $updated_results[$new_result['php_version']] += $new_result['aggregate'];
            }
        }

        return $this->result($results->mapWithKeys(function ($result) use ($groupBy, $updated_results) {
            $formated_result = $this->formatAggregateResult($result, $groupBy);
            foreach ($updated_results as $key => $value ) {
                if( key($formated_result) === $key) {
                    return [$key => $value];
                    $count++;
                }
            }
        })->all());
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
        return 'p-h-p-version';
    }
}
