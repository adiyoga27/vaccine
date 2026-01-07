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
        Schema::create('vaccine_patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('vaccine_id')->constrained()->onDelete('cascade');
            $table->date('request_date');
            $table->dateTime('vaccinated_at')->nullable();
            $table->enum('status', ['pengajuan', 'selesai'])->default('pengajuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_patients');
    }
};
