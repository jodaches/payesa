<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddostTotalToShopOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(SC_DB_PREFIX.'shop_order', function (Blueprint $table) {
            $table->float('total_cost')->nullable()->default(0)->after('total');
        });

        Schema::table(SC_DB_PREFIX.'shop_order_detail', function (Blueprint $table) {
            $table->float('cost')->nullable()->default(0)->after('price');
            $table->float('total_cost')->nullable()->default(0)->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_order', function (Blueprint $table) {
            $table->dropColumn('total_cost');
        });
    }
}
