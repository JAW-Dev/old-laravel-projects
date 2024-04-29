<?php

namespace App\Faker;

use App\Helpers\Generators\SalesGenerator;
use Faker\Generator as Faker;

class OrderStatsFaker extends BaseStatFaker {

	/**
	 * @var SalesGenerator
	 */
	protected $sales_generator = null;

	/**
	 * OrderStatsFaker constructor.
	 *
	 * @param Faker $generator
	 */
	public function __construct( Faker $generator) {
		parent::__construct( $generator );
	}

	/**
	 * @param SalesGenerator $sales_generation
	 *
	 * @return void
	 */
	public function set_sales_generator(SalesGenerator $sales_generation): void {
		$this->sales_generator = $sales_generation;
	}

	/**
	 * Returns the total sales from the sales generator
	 *
	 * @return float
	 */
	public function total_sales(): float {
		return $this->sales_generator->total_sales();
	}

	/**
	 * Returns the total orders from the sales generator
	 *
	 * @return int
	 */
	public function total_orders(): int {
		return $this->sales_generator->total_orders();
	}

	/**
	 * Returns the total items from the sales generator
	 *
	 * @return int
	 */
	public function total_items(): int {
		return $this->sales_generator->total_items();
	}
}