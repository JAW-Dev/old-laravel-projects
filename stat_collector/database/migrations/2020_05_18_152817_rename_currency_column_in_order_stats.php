<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCurrencyColumnInOrderStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_stats', function (Blueprint $table) {
            $table->renameColumn('currency', 'total_sales_localized');
        });
    }
}
