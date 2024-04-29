<?php

namespace App\Faker;

use Faker\Provider\Base;
use Faker\Generator as Faker;
use App\Helpers\Generators\SubmittedGenerator;

class BaseStatFaker extends Base {

	/**
	 * BaseStatFaker constructor.
	 *
	 * @param Faker $generator
	 */
	public function __construct( Faker $generator ) {
		parent::__construct( $generator );
	}

	/**
	 * Gets a date in the last specified amount of weeks of a 7 day interval
	 *
	 * @return string
	 */
	public function submitted(): string {
		return SubmittedGenerator::date();
	}
}