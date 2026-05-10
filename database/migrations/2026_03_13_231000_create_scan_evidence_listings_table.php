<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_evidence_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('rank');
            $table->string('ebay_item_id')->nullable();
            $table->string('title');
            $table->string('condition')->nullable();
            $table->decimal('price_value', 10, 2)->nullable();
            $table->string('price_currency', 10)->nullable();
            $table->text('item_web_url')->nullable();
            $table->text('image_url')->nullable();
            $table->string('seller_username')->nullable();
            $table->string('seller_feedback_percentage')->nullable();
            $table->unsignedInteger('seller_feedback_score')->nullable();
            $table->string('shipping_summary')->nullable();
            $table->json('buying_options')->nullable();
            $table->string('category_id')->nullable();
            $table->string('category_name')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['scan_id', 'rank']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_evidence_listings');
    }
};
