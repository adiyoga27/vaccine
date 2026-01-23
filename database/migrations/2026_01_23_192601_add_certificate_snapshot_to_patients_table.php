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
            $table->string('cert_kepala_upt_name')->nullable();
            $table->string('cert_kepala_upt_signature')->nullable();
            $table->string('cert_petugas_jurim_name')->nullable();
            $table->string('cert_petugas_jurim_signature')->nullable();
            $table->string('cert_background_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'cert_kepala_upt_name',
                'cert_kepala_upt_signature',
                'cert_petugas_jurim_name',
                'cert_petugas_jurim_signature',
                'cert_background_image'
            ]);
        });
    }
};
