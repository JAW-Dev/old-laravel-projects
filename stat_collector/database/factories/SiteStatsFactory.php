<?php

use App\SiteStats;
use App\Faker\SiteStatsFaker;
use Faker\Generator as Faker;

$factory->define(SiteStats::class, function (Faker $faker) {
	$faker->addProvider(new SiteStatsFaker($faker, env('GITHUB_OAUTH_TOKEN')));

    return [
        'site_id' => $faker->unique()->randomNumber,
        'php_version' => $faker->php_version,
		'cfw_version' => $faker->cfw_version,
		'wp_version' => $faker->wp_version,
		'mysql_version' => $faker->mysql_version,
		'server' => $faker->server,
		'php_max_upload_size' => $faker->php_max_upload_size,
		'php_default_timezone' => $faker->php_default_timezone,
		'php_soap' => $faker->php_soap,
		'php_fsockopen' => $faker->php_fsockopen,
		'php_curl' => $faker->php_curl,
		'memory_limit' => $faker->memory_limit,
		'install_date' => $faker->install_date,
		'multisite' => $faker->multisite,
		'locale' => $faker->site_locale,
		'theme' => $faker->theme,
		'gateways' => $faker->gateways,
		'wc_settings' => $faker->wc_settings,
		'cfw_settings' => $faker->cfw_settings,
		'active_plugins' => $faker->active_plugins,
		'inactive_plugins' => $faker->inactive_plugins,
		'debug_modes' => $faker->debug_modes,
		'shipping_methods' => $faker->shipping_methods,
        'submitted' => $faker->submitted,
        'created_at' => $faker->submitted
    ];
});
