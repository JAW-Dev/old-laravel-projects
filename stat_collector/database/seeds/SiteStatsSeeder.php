<?php

use Illuminate\Database\Seeder;
use App\SiteStats;

class SiteStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		factory(SiteStats::class, 100)->create();
    }
}
