<?php

namespace App\Nova\Metrics;

use App\SiteStats;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Http\Requests\NovaRequest;

class PHPSoap extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, SiteStats::orderBy('php_soap', 'desc')->limit(10), 'php_soap', 'php_soap')
        ->label(function ($value) {
            if ($value === 'yes') {
                return 'Yes';
            } else {
                return 'No';
            }
        });
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
        return 'PHP SOAP';
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
        return 'p-h-p-soap';
    }
}
