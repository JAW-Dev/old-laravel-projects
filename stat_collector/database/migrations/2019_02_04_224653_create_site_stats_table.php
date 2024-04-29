<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_stats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('site_id');
            $table->text('php_version');
            $table->text('cfw_version');
            $table->text('wp_version');
            $table->text('mysql_version');
			$table->text('server');
            $table->text('php_max_upload_size');
            $table->text('php_default_timezone');
            $table->text('php_soap');
            $table->text('php_fsockopen');
            $table->text('php_curl');
            $table->text('memory_limit');
            $table->date('install_date');
            $table->boolean('multisite');
			$table->text('locale');
			$table->json('theme');
			$table->json('gateways');
			$table->json('wc_settings');
			$table->json('cfw_settings');
			$table->json('shipping_methods');
			$table->json('active_plugins');
			$table->json('inactive_plugins');
			$table->json('debug_modes');
            $table->date('submitted')->default(Carbon::now()->subDay()->format('Y-m-d'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_stats');
    }
}
