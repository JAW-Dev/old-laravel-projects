<?php

use App\OrderStats;
use Illuminate\Database\Seeder;

class OrderStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		factory(OrderStats::class, 100)->create();
    }
}
