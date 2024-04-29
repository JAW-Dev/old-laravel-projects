<?php

use App\OrderStats;
use App\Helpers\Generators\SalesGenerator;
use App\Faker\OrderStatsFaker;
use Faker\Generator as Faker;

$factory->define(OrderStats::class, function (Faker $faker) {
	$sales_generator = new SalesGenerator($faker, 10000, 100000000);
	$faker->addProvider(new OrderStatsFaker($faker));

	// Sales gen needs to be set each loop for new sales
	$faker->set_sales_generator($sales_generator);

    return [
        'site_id' => $faker->unique()->randomNumber,
		'total_sales' => $faker->total_sales,
		'total_orders' => $faker->total_orders,
		'total_items' => $faker->total_items,
        'submitted' => $faker->submitted,
        'created_at' => $faker->submitted
    ];
});
