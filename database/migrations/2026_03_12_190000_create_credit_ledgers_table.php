<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credit_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 50);
            $table->integer('amount');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        $now = now();

        $teams = DB::table('teams')->select('id', 'user_id')->get();

        foreach ($teams as $team) {
            DB::table('credit_ledgers')->insert([
                'team_id' => $team->id,
                'user_id' => $team->user_id,
                'type' => 'starter_grant',
                'amount' => 50,
                'description' => 'Starter credits for a new Ghostfrog workspace.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_ledgers');
    }
};
