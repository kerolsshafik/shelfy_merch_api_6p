<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeImageNullableInInvoiceProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // php artisan migrate --path=/database/migrations/2025_05_06_134019_make_image_nullable_in_invoice_products_table.php
    public function up()
    {
        // Schema::table('invoice_products', function (Blueprint $table) {
        Schema::connection('mysql')->table('invoice_products', function (Blueprint $table) {

            $table->string('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_products', function (Blueprint $table) {
            //             $table->string('image')->nullable(false)->change(); // revert if needed

        });
    }
}
