<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitProductPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('rose_visit_product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visit_id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('price', 12, 2);
            $table->timestamps();

            $table->unique(['visit_id', 'store_id', 'product_id'], 'visit_store_product_unique');
            $table->index(['visit_id', 'store_id'], 'visit_store_index');
            $table->index(['product_id'], 'product_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rose_visit_product_prices');
    }
}
