<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credit_ledgers', function (Blueprint $table) {
            $table->string('reference')->nullable()->unique()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('credit_ledgers', function (Blueprint $table) {
            $table->dropUnique(['reference']);
            $table->dropColumn('reference');
        });
    }
};
