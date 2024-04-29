<?php

namespace App\Helpers\Generators;

use Faker\Generator as Faker;
use Faker\Factory as FakerFactory;

class VersionGenerator {
	/**
	 * Returns a random version number between specified values
	 *
	 * @param int[] $major
	 * @param int[] $minor
	 * @param int[] $release
	 *
	 * @return string
	 */
	public static function random_version_number(
		array $major = [1, 6],
		array $minor = [0, 15],
		array $release = [0, 26]
	): string {
		/** @var Faker $faker */
		$faker = FakerFactory::create();

		if ( count( $major ) != 2 || count( $minor ) != 2 || count( $release ) != 2 ) {
			return '';
		}

		$major = $faker->numberBetween(abs((int)$major[0]), abs((int)$major[1]));
		$minor = $faker->numberBetween(abs((int)$minor[0]), abs((int)$minor[1]));
		$release = $faker->numberBetween(abs((int)$release[0]), abs((int)$release[1]));

		return implode('.', [$major, $minor, $release]);
	}
}