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
        Schema::create('scan_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('summary')->nullable();
            $table->json('missing_attributes')->nullable();
            $table->json('competitor_insights')->nullable();
            $table->json('listing_actions')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_reports');
    }
};
