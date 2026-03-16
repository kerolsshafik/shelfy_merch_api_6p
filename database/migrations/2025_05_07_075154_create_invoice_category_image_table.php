<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceCategoryImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_category_image', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_category_id')->constrained('invoice_categories')->onDelete('cascade'); // creates the 'key' column and foreign key constraint
            $table->string('image');
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
        Schema::dropIfExists('invoice_category_image');
    }
}
