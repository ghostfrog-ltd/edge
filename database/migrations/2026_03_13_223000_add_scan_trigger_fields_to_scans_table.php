<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scans', function (Blueprint $table): void {
            $table->string('scan_type')->default('keyword')->after('user_id');
            $table->string('ebay_category_id')->nullable()->after('category');
            $table->string('competitor_store_url')->nullable()->after('ebay_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('scans', function (Blueprint $table): void {
            $table->dropColumn([
                'scan_type',
                'ebay_category_id',
                'competitor_store_url',
            ]);
        });
    }
};
