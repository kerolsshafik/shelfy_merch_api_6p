<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosMImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_m_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_m_id');
            $table->string('image_path');
            $table->timestamps();

            $table->foreign('pos_m_id')->references('id')->on('pos_ms')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_m_images');
    }
}
