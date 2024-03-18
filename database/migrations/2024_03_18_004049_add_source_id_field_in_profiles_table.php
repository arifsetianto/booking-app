<?php

use App\ValueObject\Gender;
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
        Schema::table('profiles', function (Blueprint $table) {
            if (Schema::hasColumn('profiles', 'gender')) {
                $table->dropColumn('gender');
            }

            if (Schema::hasColumn('profiles', 'religion_id')) {
                $table->dropConstrainedForeignId('religion_id');
            }

            if (Schema::hasColumn('profiles', 'address')) {
                $table->dropColumn('address');
            }

            if (Schema::hasColumn('profiles', 'sub_district_id')) {
                $table->dropConstrainedForeignId('sub_district_id');
            }

            $table->uuid('source_id')->index()->nullable();
            $table->foreign('source_id')->references('id')->on('sources');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('source_id');
            $table->enum('gender', Gender::getValues())->nullable()->index();
            $table->uuid('religion_id')->index()->nullable();
            $table->foreign('religion_id')->references('id')->on('religions');
            $table->text('address')->nullable();
            $table->uuid('sub_district_id')->index()->nullable();
            $table->foreign('sub_district_id')->references('id')->on('sub_districts');
        });
    }
};
