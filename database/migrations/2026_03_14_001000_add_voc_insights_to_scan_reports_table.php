<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scan_reports', function (Blueprint $table): void {
            $table->json('voc_insights')->nullable()->after('schema_audit');
        });
    }

    public function down(): void
    {
        Schema::table('scan_reports', function (Blueprint $table): void {
            $table->dropColumn('voc_insights');
        });
    }
};
