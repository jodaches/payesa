<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProductAddLowerPriceColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(SC_DB_PREFIX.'shop_product', function (Blueprint $table) {
            $table->float('lower_price')->default(0)->after('price');
        });

        Schema::table(SC_DB_PREFIX.'shop_order_detail', function (Blueprint $table) {
            $table->float('lower_price')->default(0)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(SC_DB_PREFIX.'shop_product', function (Blueprint $table) {
            $table->dropcolumn('lower_price');
        });

        Schema::table(SC_DB_PREFIX.'shop_order_detail', function (Blueprint $table) {
            $table->dropcolumn('lower_price');            
        });
    }
}
