<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	// Seeders to only run in production
    	$productionSeeders = [];

    	// Seeders to only run on local
    	$stagingSeeders = [
			UsersTableSeeder::class,
			OrderStatsSeeder::class,
            SiteStatsSeeder::class,
            ArticlesTableSeeder::class
		];

    	// Get the seeders
    	$seeders = App::environment('local') ? $stagingSeeders : $productionSeeders;

    	// Run them
		$this->call($seeders);
    }
}
