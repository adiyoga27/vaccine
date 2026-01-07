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
        Schema::table('vaccine_schedules', function (Blueprint $table) {
            $table->foreignId('posyandu_id')->nullable()->constrained()->onDelete('cascade')->after('village_id');
            // We make village_id nullable if we want to migrate data, but for now we just add the new column.
            // Ideally schedule belongs to posyandu, which belongs to village.
             $table->unsignedBigInteger('village_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vaccine_schedules', function (Blueprint $table) {
            $table->dropForeign(['posyandu_id']);
            $table->dropColumn('posyandu_id');
            $table->unsignedBigInteger('village_id')->nullable(false)->change();
        });
    }
};
