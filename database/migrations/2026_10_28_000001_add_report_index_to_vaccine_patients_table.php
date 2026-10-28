<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vaccine_patients', function (Blueprint $table) {
            $table->index(['village_id', 'status', 'vaccinated_at'], 'vp_village_status_vaccinated_idx');
        });
    }

    public function down(): void
    {
        Schema::table('vaccine_patients', function (Blueprint $table) {
            $table->dropIndex('vp_village_status_vaccinated_idx');
        });
    }
};
