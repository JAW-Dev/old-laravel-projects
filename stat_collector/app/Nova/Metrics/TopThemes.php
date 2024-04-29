<?php

namespace App\Nova\Metrics;

use App\SiteStats;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Http\Requests\NovaRequest;

class TopThemes extends Partition
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
        ->all('theme')
        ->toArray();

        $themes = [];

        foreach ($collection as $item) {
            $theme = $item['theme'];
            $themes[] = $theme['name'];
        }

        $themes = array_replace($themes,array_fill_keys(array_keys($themes, null),''));
        $themes = array_count_values($themes);

        uasort($themes, function($a, $b) {
            return $this->calculateSort($a, $b);
        });

        // // Limit to 10 results.
        $themes = array_slice($themes, 0, 10);

        return $this->result( $themes );
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
        return 'top-themes';
    }
}
