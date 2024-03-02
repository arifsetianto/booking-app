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
        Schema::table('orders', function (Blueprint $table) {
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('confirmed_at');
            $table->dropColumn('verified_at');
            $table->dropColumn('completed_at');
            $table->dropColumn('rejected_at');
            $table->dropColumn('canceled_at');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('paid_at');
            $table->dropColumn('canceled_at');
        });
    }
};
