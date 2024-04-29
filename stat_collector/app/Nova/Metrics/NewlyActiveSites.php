<?php

namespace App\Nova\Metrics;

use App\SiteStats;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Value;

class NewlyActiveSites extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        return $this->count($request, SiteStats::class);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            7 => '1 Week',
            30 => '30 Days',
            182 => '6 Months',
            365 => '1 Year',
            99999999999 => 'All Time'
        ];
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
        return 'newly-active-sites';
    }
}
