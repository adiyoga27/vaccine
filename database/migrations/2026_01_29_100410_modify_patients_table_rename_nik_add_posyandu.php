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
        Schema::table('patients', function (Blueprint $table) {
            // Check if mother_nik exists (it should based on previous search)
            if (Schema::hasColumn('patients', 'mother_nik')) {
                $table->renameColumn('mother_nik', 'nik');
            } else {
                // Determine layout: After mother_name
                $table->string('nik', 16)->nullable()->after('mother_name');
            }
            
            // Add posyandu_id
            $table->foreignId('posyandu_id')->nullable()->after('village_id')->constrained('posyandus')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
             // Revert posyandu_id
            $table->dropForeign(['posyandu_id']);
            $table->dropColumn('posyandu_id');

            // Revert NIK rename
            if (Schema::hasColumn('patients', 'nik')) {
                $table->renameColumn('nik', 'mother_nik');
            }
        });
    }
};
