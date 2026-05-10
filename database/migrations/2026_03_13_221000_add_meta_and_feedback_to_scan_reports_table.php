<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scan_reports', function (Blueprint $table): void {
            $table->json('report_meta')->nullable()->after('listing_actions');
            $table->string('feedback_rating')->nullable()->after('report_meta');
            $table->text('feedback_notes')->nullable()->after('feedback_rating');
            $table->timestamp('feedback_submitted_at')->nullable()->after('feedback_notes');
        });
    }

    public function down(): void
    {
        Schema::table('scan_reports', function (Blueprint $table): void {
            $table->dropColumn([
                'report_meta',
                'feedback_rating',
                'feedback_notes',
                'feedback_submitted_at',
            ]);
        });
    }
};
