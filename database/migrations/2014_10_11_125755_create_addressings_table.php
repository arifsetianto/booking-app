<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->index();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('region_id')->index();
            $table->string('en_name')->index();
            $table->string('th_name')->index();

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('city_id')->index();
            $table->string('en_name')->index();
            $table->string('th_name')->index();

            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        });

        Schema::create('sub_districts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('district_id')->index();
            $table->string('en_name')->index();
            $table->string('th_name')->index();
            $table->integer('zip_code')->index();

            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_districts');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('regions');
    }
};
