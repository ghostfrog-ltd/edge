<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->string('engine_job_id')->nullable()->after('status');
            $table->timestamp('engine_dispatched_at')->nullable()->after('queued_at');
        });
    }

    public function down(): void
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->dropColumn([
                'engine_job_id',
                'engine_dispatched_at',
            ]);
        });
    }
};
