<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('main')->unsigned()->default(1);
            $table->unsignedBigInteger('rel_id')->default(0);
            $table->unsignedBigInteger('storekey')->default(0);
            $table->integer('employee_id')->default(0);
            $table->string('name', 100)->index();
            $table->string('phone', 30)->index();
            $table->string('address', 100)->nullable();
            $table->string('city', 50)->default('City');
            $table->string('region', 30)->default('Region');
            $table->string('country', 50)->default('Country');
            $table->string('postbox', 20)->default('Post Box');
            $table->string('email', 90)->index();
            $table->string('picture', 100)->nullable();
            $table->string('company', 100)->default('Company');
            $table->string('taxid', 100)->default('Tax ID');
            $table->string('name_s', 100)->nullable();
            $table->string('phone_s', 100)->nullable();
            $table->string('email_s', 100)->nullable();
            $table->string('address_s', 100)->nullable();
            $table->string('city_s', 100)->nullable();
            $table->string('region_s', 100)->nullable();
            $table->string('country_s', 100)->nullable();
            $table->string('postbox_s', 100)->nullable();
            $table->decimal('balance', 16, 2)->default(0.00);
            $table->string('docid')->nullable();
            $table->string('custom1')->nullable();
            $table->unsignedInteger('ins')->default(0)->index();
            $table->unsignedInteger('active')->default(1);
            $table->string('password', 191)->nullable();
            $table->unsignedInteger('role_id')->default(0);
            $table->string('remember_token', 100)->nullable();
            $table->string('referral_id')->nullable();
            $table->string('account_no')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('birth_date')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->tinyInteger('auto_substation')->default(0);
            $table->string('store_name')->nullable(false);
            $table->string('segment')->nullable(false);
            $table->string('governorate')->default('cairo');
            $table->string('lat')->nullable(false);
            $table->string('lng')->nullable(false);
            $table->tinyInteger('store_status')->nullable(false);
            $table->unsignedBigInteger('points')->default(0);
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
        Schema::dropIfExists('stores');
    }
}
