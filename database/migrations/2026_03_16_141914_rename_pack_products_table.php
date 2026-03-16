<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePackProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Rename the existing table 'pack_products' → 'rose_pack_products'
        Schema::rename('pack_products', 'rose_pack_products');
    }

    public function down(): void
    {
        Schema::rename('rose_pack_products', 'pack_products');
    }
}
