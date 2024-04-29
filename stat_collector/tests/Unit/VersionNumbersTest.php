<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\Filters\VersionFilter;
use App\Helpers\Generators\VersionGenerator;

/**
 * Unit Test that tests the VersionNumbers class and its associated functionality
 *
 * @package Tests\Unit
 * @author Brandon Tassone <brandon@objectiv.co>
 * @version 1.0.0
 */
class VersionNumbersTest extends TestCase
{
	/**
	 * Versions to test
	 *
	 * @var array
	 */
	protected $versions = [];

	/**
	 * Error messages for the default filter tests
	 *
	 * @var \PHPunit\Framework\string|void[]
	 */
	protected $default_filter_error_messages = [
		'min_major_version' => 'Major version number less than specified version number',
		'version_format_count' => 'Version format count was not the expected length of 3 (major, minor, release)',
		'version_type_not_int' => 'The version type (major, minor, release) was found not to be an integer',
		'random_version_number_not_int' => 'The random number returned for a version part was deemed not a valid integer',
		'random_version_number_count' => 'The returned string is the incorrect array length when exploded',
		'random_version_number_less_than_zero' => 'The returned number in the version set is less than 0'
	];

	public function setUp() {
		parent::setUp();

		$this->versions = [
			(object) ['name' => '1.2.3'],
			(object) ['name' => '2.25.1'],
			(object) ['name' => '2.3.25'],
			(object) ['name' => '3.4.1'],
			(object) ['name' => '4.25.1'],
			(object) ['name' => '5.12.1'],
			(object) ['name' => 'beta-5.2.1'],
			(object) ['name' => '2.2.3-something'],
			(object) ['name' => 'kittens']
		];
	}

	/**
	 * Tests the default version number filtering
	 */
	public function testVersionNumberDefaultFilter() {
		// Versions to test
		$versions = VersionFilter::parse_versions($this->versions);

		array_walk($versions, function($version) {
			// If valid versions explode should work
			$version_types = array_map('intval', explode('.', $version));

			// Must be 3 for valid version
			$this->assertCount(3, $version_types, $this->default_filter_error_messages['version_format_count']);

			// Make sure all versions are numeric
			array_walk($version_types, function($version_type) {
				$this->assertIsInt($version_type, $this->default_filter_error_messages['version_type_not_int']);
			});
		});
	}

	/**
	 * Tests the default version number filtering and also checks the minimum major version number
	 */
	public function testVersionNumberDefaultFilterWithMinMajorVersion() {
		$min_major_versions = [2, 3, 5, 1, 4];

		// For each version number in the test, retrieve the values that match
		array_walk($min_major_versions, function($min_major_version) {
			// Versions to traverse with min major version passed in
			$versions = VersionFilter::parse_versions($this->versions, $min_major_version);

			/**
			 * Test each returned value in the set for the filtered versions by min major version and make sure its greater
			 * than or equal to the major version number
			 */
			array_walk($versions, function($version) use ($min_major_version) {
				$version_types = array_map('intval', explode('.', $version));
				$major_version = $version_types[0];

				$this->assertGreaterThanOrEqual($min_major_version, $major_version, $this->default_filter_error_messages['min_major_version']);
			});
		});
	}

	/**
	 * Test the random version string generator
	 */
	public function testRandomVersionNumbers() {
		$version_number_results = [
			VersionGenerator::random_version_number(),
			VersionGenerator::random_version_number(['kittens', 'dogs'], ['-1', 200], [-30, -60])
		];

		// Make sure all numbers check out as integers
		array_walk($version_number_results, function($result) {
			$numbers = explode('.', $result);

			$this->assertCount(3, $numbers, $this->default_filter_error_messages['random_version_number_count']);

			$this->assertIsInt(intval($numbers[0]), $this->default_filter_error_messages['random_version_number_not_int']);
			$this->assertIsInt(intval($numbers[1]), $this->default_filter_error_messages['random_version_number_not_int']);
			$this->assertIsInt(intval($numbers[2]), $this->default_filter_error_messages['random_version_number_not_int']);

			$this->assertGreaterThanOrEqual(0, $numbers[0], $this->default_filter_error_messages['random_version_number_less_than_zero']);
			$this->assertGreaterThanOrEqual(0, $numbers[1], $this->default_filter_error_messages['random_version_number_less_than_zero']);
			$this->assertGreaterThanOrEqual(0, $numbers[2], $this->default_filter_error_messages['random_version_number_less_than_zero']);
		});
	}
}
