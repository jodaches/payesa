<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRemoveLowerPriceFromOrderDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(SC_DB_PREFIX.'shop_order_detail', function (Blueprint $table) {
            $table->dropcolumn('lower_price');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(SC_DB_PREFIX.'shop_order_detail', function (Blueprint $table) {
            $table->float('lower_price')->default(0)->after('name');
        });
    }
}
