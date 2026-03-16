<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_material_id');
            $table->string('image_path'); // Store the image filename or path
            $table->timestamps();

            $table->foreign('pos_material_id')
                ->references('id')->on('pos_materials')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_images');
    }
}
