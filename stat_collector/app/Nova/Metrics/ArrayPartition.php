<?php

namespace App\Nova\Metrics;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Metrics\Partition;

class ArrayPartition extends Partition
{

	/**
	 * The array column to aggregate
	 *
	 * @var string
	 */
	public $column;

	/**
	 * @var string
	 */
	public $model;

	/**
	 * @var array
	 */
	public $except = [
		'checkout-for-woocommerce/checkout-for-woocommerce.php',
		'hello.php',
		'woocommerce/woocommerce.php',
		'wordpress-importer/wordpress-importer.php'
	];

	/**
	 * ArrayPartition constructor.
	 *
	 * @param string|null $component
	 */
	public function __construct( ?string $component = null ) {
		parent::__construct( $component );

		$this->except();
	}

	/**
	 * @param Request $request
	 *
	 * @return \Laravel\Nova\Metrics\PartitionResult
	 */
    public function calculate(Request $request)
    {
    	$collection = with(new $this->model)
			->all($this->column)
			->each(function($item) {
                $data = $item[$this->column];

				foreach($data as $index => $activePlugin) {
					if(in_array($activePlugin, $this->except)) {
						unset($data[$index]);
					}
				}

				$item[$this->column] = $data;

				return $item;
			})
			->arrayCountValues($this->column)
			->toArray();

    	uasort($collection, function($a, $b) {
    		return $this->calculateSort($a, $b);
        });

        // Limit to 5 results.
        $collection = array_slice($collection, 0, 10);

        return $this->result( $collection );
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
	 * @param string|null $column
	 */
    public function column(string $column): void {
    	$this->column = $column;
	}

	/**
	 * @param string|null $model
	 */
	public function model(string $model): void {
    	$this->model = $model;
	}

	/**
	 * @param array $except
	 */
	public function except(array $except = []): void {
		$this->except = array_merge($this->except, $except);
	}

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'array-result-partition';
    }
}
