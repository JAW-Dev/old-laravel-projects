<?php

namespace App\Helpers\Generators;

use Faker\Generator as Faker;

class SalesGenerator {

	protected $faker = null;

	/**
	 * The min total sales the site can have
	 *
	 * @var int
	 */
	protected $min_total_sales = 0;

	/**
	 * The max total sales the site can have
	 *
	 * @var int
	 */
	protected $max_total_sales = 0;

	/**
	 * The max decimal places to calculate dollar amounts to
	 *
	 * @var int
	 */
	protected $max_decimal_places = 0;

	/**
	 * Multiplier to use to derive tax info from total sales
	 *
	 * @var float
	 */
	protected $tax_mult = 0.0;

	/**
	 * Multiplier to use to derive net sales info from total sales
	 *
	 * @var float
	 */
	protected $net_mult = 0.0;

	/**
	 * Multiplier to use to derive coupon info from total sales
	 *
	 * @var float
	 */
	protected $coupon_mult = 0.0;

	/**
	 * Multiplier to use to derive shipping info from total sales
	 *
	 * @var float
	 */
	protected $shipping_mult = 0.0;

	/**
	 * Multiplier to use to derive refunds info from total sales
	 *
	 * @var float
	 */
	protected $refunds_mult = 0.0;

	/**
	 * Multiplier to use to derive orders info from total sales
	 *
	 * @var float
	 */
	protected $orders_mult = 0.0;

	/**
	 * Multiplier to use to derive refunded orders info from total orders
	 *
	 * @var float
	 */
	protected $refunded_orders_mult = 0.0;

	/**
	 * Multiplier to use to derive items info from total orders
	 *
	 * @var int
	 */
	protected $items_mult = 0;

	/**
	 * The generated total sales number used for calculations
	 *
	 * @var float
	 */
	protected $total_sales = 0.0;

	/**
	 * The generated total orders based off the total sales
	 *
	 * @var int
	 */
	protected $total_orders = 0;

	protected static $instance;

	/**
	 * SalesGenerator constructor.
	 *
	 * @param Faker $faker
	 * @param int $min_total_sales
	 * @param int $max_total_sales
	 * @param int $max_decimal_places
	 * @param float $tax_mult
	 * @param float $net_mult
	 * @param float $coupon_mult
	 * @param float $shipping_mult
	 * @param float $refunds_mult
	 * @param float $orders_mult
	 * @param float $refunded_orders_mult
	 * @param int $items_mult
	 */
	public function __construct(
		Faker $faker,
		int $min_total_sales,
		int $max_total_sales,
		int $max_decimal_places = 2,
		float $tax_mult = 0.05,
		float $net_mult = 0.8,
		float $coupon_mult = 0.2,
		float $shipping_mult = 0.15,
		float $refunds_mult = 0.02,
		float $orders_mult = 0.02,
		float $refunded_orders_mult = 0.0002,
		int $items_mult = 5
	) {
		$this->faker = $faker;
		$this->min_total_sales = $min_total_sales;
		$this->max_total_sales = $max_total_sales;
		$this->max_decimal_places = $max_decimal_places;
		$this->tax_mult = $tax_mult;
		$this->net_mult = $net_mult;
		$this->coupon_mult = $coupon_mult;
		$this->shipping_mult = $shipping_mult;
		$this->refunds_mult = $refunds_mult;
		$this->orders_mult = $orders_mult;
		$this->refunded_orders_mult = $refunded_orders_mult;
		$this->items_mult = $items_mult;

		$this->set_total_sales();
		$this->set_total_orders();

		SalesGenerator::$instance = $this;
	}

	public static function instance() {

	}

	/**
	 * Generates a random total sales float from specified decimal places, min total sales, and max total sales
	 *
	 * @return void
	 */
	protected function set_total_sales(): void {
		$this->total_sales = $this->faker->randomFloat($this->max_decimal_places, $this->min_total_sales, $this->max_total_sales);
	}

	/**
	 * Generates a random total orders amount based off of the total sales and a multiplier
	 *
	 * @return void
	 */
	protected function set_total_orders(): void {
		$this->total_orders = intval($this->faker->numberBetween(0, $this->total_sales * $this->orders_mult));
	}

	/**
	 * Returns the total sales
	 *
	 * @return float
	 */
	public function total_sales(): float {
		return $this->total_sales;
	}

	/**
	 * Returns the net sales
	 *
	 * @return float
	 */
	public function net_sales(): float {
		return $this->total_sales() * $this->net_mult;
	}

	/**
	 * Returns the total tax
	 *
	 * @return float
	 */
	public function total_tax(): float {
		return $this->total_sales() * $this->tax_mult;
	}

	/**
	 * Returns the total shipping
	 *
	 * @return float
	 */
	public function total_shipping(): float {
		return $this->total_sales() * $this->shipping_mult;
	}

	/**
	 * Returns the total shipping tax
	 *
	 * @return float
	 */
	public function total_shipping_tax(): float {
		return $this->total_shipping() * $this->tax_mult;
	}

	/**
	 * Returns the total refunds
	 *
	 * @return float
	 */
	public function total_refunds(): float {
		return $this->total_sales() * $this->refunds_mult;
	}

	/**
	 * Returns the total tax refunded
	 *
	 * @return float
	 */
	public function total_tax_refunded(): float {
		return $this->total_refunds() * $this->tax_mult;
	}

	/**
	 * Returns the total shipping refunded
	 *
	 * @return float
	 */
	public function total_shipping_refunded(): float {
		return $this->total_refunds() * $this->shipping_mult;
	}

	/**
	 * Returns the total shipping tax refunded
	 *
	 * @return float
	 */
	public function total_shipping_tax_refunded(): float {
		return $this->total_shipping_refunded() * $this->tax_mult;
	}

	/**
	 * Returns the total coupon amount
	 *
	 * @return float
	 */
	public function total_coupons(): float {
		return $this->total_sales() * $this->coupon_mult;
	}

	/**
	 * Returns the total amount of orders
	 *
	 * @return int
	 */
	public function total_orders(): int {
		return $this->total_orders;
	}

	/**
	 * Returns the total amount of refunded orders
	 *
	 * @return int
	 */
	public function total_refunded_orders(): int {
		return intval($this->total_orders() * $this->refunded_orders_mult);
	}

	/**
	 * Returns the total amount of items
	 *
	 * @return int
	 */
	public function total_items(): int {
		return intval($this->total_orders() * $this->items_mult);
	}

	/**
	 * Returns the average sales
	 *
	 * @return float
	 */
	public function average_sales(): float {
		return $this->net_sales() / $this->total_orders();
	}

	/**
	 * Returns the average total sales
	 *
	 * @return float
	 */
	public function average_total_sales(): float {
		return $this->total_sales() / $this->total_orders();
	}
}