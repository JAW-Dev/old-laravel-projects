<?php

namespace App\Helpers\Generators;

use Carbon\Carbon;

class SubmittedGenerator {

	/**
	 * @param int $max_weeks
	 * @param string $format
	 *
	 * @return string
	 */
	public static function date($max_weeks = 52, $format = 'Y-m-d') {
		$date = Carbon::now();

		return $date->subWeeks(rand(1, $max_weeks))->format($format);
	}
}