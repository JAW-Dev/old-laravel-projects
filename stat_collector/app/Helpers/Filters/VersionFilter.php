<?php

namespace App\Helpers\Filters;

use App\Helpers\Basic\VersionMatched;

/**
 * Filter class that parses and tests for valid version numbers
 *
 * @package App\Helpers\Filters
 * @author Brandon Tassone <brandon@objectiv.co>
 * @version 1.0.0
 */
class VersionFilter {

	/**
	 * Test the version to see if it fits our parameters for what we need
	 *
	 * @param string $version
	 * @param int $minimum_major_version
	 * @param string $pattern
	 *
	 * @return VersionMatched
	 */
	public static function test_version(
		string $version,
		int $minimum_major_version = 0,
		string $pattern = '/(\d*\.\d*\.\d*)/'
	): VersionMatched {
		$matches = [];
		$test = preg_match($pattern, $version, $matches) && intval(explode('.', $matches[0])[0]) >= $minimum_major_version;
		$version_matched = new VersionMatched($test, $test ? $matches[0] : '');

		return $version_matched;
	}

	/**
	 * Parse the versions from the return data and test it to see if it matches our parameters
	 *
	 * @param array $data
	 * @param int $minimum_major_version
	 * @param array $versions
	 * @param array $test_version
	 *
	 * @return array
	 */
	public static function parse_versions(
		array $data,
		int $minimum_major_version = 0,
		array $versions = [],
		array $test_version = [VersionFilter::class, 'test_version']
	): array {
		array_walk($data, function($release) use (&$versions, $test_version, $minimum_major_version) {
			$version = $release->name;
			/** @var VersionMatched $version_matched */
			$version_matched = call_user_func($test_version, $version, $minimum_major_version);

			if($version_matched->matched) {
				$versions[] = $version_matched->matching;
			}
		});

		return $versions;
	}
}