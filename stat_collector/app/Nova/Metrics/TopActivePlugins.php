<?php

namespace App\Nova\Metrics;

use App\SiteStats;

class TopActivePlugins extends ArrayPartition
{
    /**
     * TopPlugins constructor.
     *
     * @param string|null $component
     */
    public function __construct( ?string $component = null ) {
        parent::__construct( $component );

		// Change this from Model to your model
        $this->model(SiteStats::class);

		// Set a db column
        $this->column('active_plugins');
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
        return 'top-plugins';
    }
}
