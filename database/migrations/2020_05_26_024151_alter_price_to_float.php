<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPriceToFloat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(SC_DB_PREFIX.'shop_order_detail', function (Blueprint $table) {
            $table->float('price')->default(0)->change();
            $table->float('total_price')->default(0)->change();
            $table->float('tax')->default(0)->change();
        });
        Schema::table(SC_DB_PREFIX.'shop_product', function (Blueprint $table) {
            $table->float('price')->nullable()->default(0)->change();
            $table->float('cost')->nullable()->default(0)->change();
        });

        Schema::table(SC_DB_PREFIX.'shop_product_promotion', function (Blueprint $table) {
            $table->float('price_promotion')->change();
        });

        Schema::table(SC_DB_PREFIX.'shop_order', function (Blueprint $table) {
            $table->float('subtotal')->nullable()->default(0)->change();
            $table->float('tax')->nullable()->default(0)->change();
            $table->float('total')->nullable()->default(0)->change();
            $table->integer('balance')->nullable()->default(0)->change();
        });

        Schema::table(SC_DB_PREFIX.'shop_order_total', function (Blueprint $table) {
            $table->float('value')->default(0)->change();
        });

        Schema::table(SC_DB_PREFIX.'shop_tax', function (Blueprint $table) {
            $table->integer('value')->default(0)->change();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        echo "nothing";
    }
}
