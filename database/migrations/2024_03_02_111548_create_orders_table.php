<?php

use App\ValueObject\Gender;
use App\ValueObject\OrderStatus;
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
        Schema::create('sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100)->index();
        });

        Schema::create('designations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100)->index();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('batch_id')->index();
            $table->string('code', 15)->unique()->index();
            $table->integer('qty');
            $table->bigInteger('amount');
            $table->text('comment')->nullable();
            $table->string('email')->index();
            $table->string('name')->index();
            $table->string('phone', 30)->nullable();
            $table->string('instagram', 100);
            $table->uuid('source_id')->index();
            $table->enum('status', OrderStatus::getValues())->index();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('cascade');
            $table->foreign('source_id')->references('id')->on('sources');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id')->index();
            $table->integer('qty');
            $table->bigInteger('amount');
            $table->string('receiver_en_name')->index();
            $table->string('receiver_th_name')->index();
            $table->enum('gender', Gender::getValues())->index();
            $table->uuid('religion_id')->index();
            $table->uuid('designation_id')->index();
            $table->string('identity_file');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('religion_id')->references('id')->on('religions');
            $table->foreign('designation_id')->references('id')->on('designations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('sources');
    }
};
