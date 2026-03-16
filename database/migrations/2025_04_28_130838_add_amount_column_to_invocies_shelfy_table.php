<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountColumnToInvociesShelfyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    //  php artisan migrate --path=/database/migrations/2025_04_28_130838_add_amount_column_to_invocies_shelfy_table.php
    public function up()
    {
        Schema::table('invocies_shelfy', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->after('points')->default(0.00);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('invocies_shelfy', function (Blueprint $table) {
            // $table->dropColumn('amount');
        });
    }
}
