<?php

namespace App\Nova\Metrics;

use App\SiteStats;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Http\Requests\NovaRequest;

class WPVersion extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, SiteStats::orderBy('aggregate', 'desc')->limit(10), 'wp_version', 'wp_version');
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
        return 'WordPress Version';
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
        return 'w-p-version';
    }
}
