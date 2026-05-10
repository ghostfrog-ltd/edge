<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_offers', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('key')->unique();
            $table->string('name');
            $table->text('summary');
            $table->string('price_label');
            $table->string('currency', 3)->default('gbp');
            $table->unsignedInteger('amount_minor')->default(0);
            $table->string('billing_interval')->nullable();
            $table->unsignedInteger('monthly_credits')->nullable();
            $table->unsignedInteger('credits')->nullable();
            $table->string('credits_label')->nullable();
            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('pricing_offers')->insert([
            [
                'type' => 'plan',
                'key' => 'free',
                'name' => 'Free',
                'summary' => 'A lightweight try-before-you-pay tier so sellers can understand the workflow before buying credits or a subscription.',
                'price_label' => 'PS0',
                'currency' => 'gbp',
                'amount_minor' => 0,
                'billing_interval' => null,
                'monthly_credits' => 10,
                'credits' => null,
                'credits_label' => 'Small starter allowance',
                'stripe_product_id' => null,
                'stripe_price_id' => null,
                'is_active' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'plan',
                'key' => 'starter',
                'name' => 'Starter',
                'summary' => 'For solo sellers who want regular niche scans without team complexity.',
                'price_label' => 'PS29 / month',
                'currency' => 'gbp',
                'amount_minor' => 2900,
                'billing_interval' => 'month',
                'monthly_credits' => 50,
                'credits' => null,
                'credits_label' => '50 credits each month',
                'stripe_product_id' => null,
                'stripe_price_id' => config('billing.plans.starter.stripe_price_id'),
                'is_active' => true,
                'sort_order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'plan',
                'key' => 'pro',
                'name' => 'Pro',
                'summary' => 'For power sellers who need deeper usage and more frequent report runs.',
                'price_label' => 'PS79 / month',
                'currency' => 'gbp',
                'amount_minor' => 7900,
                'billing_interval' => 'month',
                'monthly_credits' => 200,
                'credits' => null,
                'credits_label' => '200 credits each month',
                'stripe_product_id' => null,
                'stripe_price_id' => config('billing.plans.pro.stripe_price_id'),
                'is_active' => true,
                'sort_order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'plan',
                'key' => 'team',
                'name' => 'Team',
                'summary' => 'For shared workspaces, agencies, and higher-volume operators.',
                'price_label' => 'PS199 / month',
                'currency' => 'gbp',
                'amount_minor' => 19900,
                'billing_interval' => 'month',
                'monthly_credits' => 600,
                'credits' => null,
                'credits_label' => '600 pooled credits each month',
                'stripe_product_id' => null,
                'stripe_price_id' => config('billing.plans.team.stripe_price_id'),
                'is_active' => true,
                'sort_order' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'top_up',
                'key' => 'boost_25',
                'name' => '25-credit boost',
                'summary' => 'A small refill for testing, urgent rescans, or one-off client work.',
                'price_label' => 'PS15 one-off',
                'currency' => 'gbp',
                'amount_minor' => 1500,
                'billing_interval' => null,
                'monthly_credits' => null,
                'credits' => 25,
                'credits_label' => null,
                'stripe_product_id' => null,
                'stripe_price_id' => config('billing.top_ups.boost_25.stripe_price_id'),
                'is_active' => true,
                'sort_order' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'top_up',
                'key' => 'boost_100',
                'name' => '100-credit boost',
                'summary' => 'The sensible mid-pack for extra research bursts without changing subscription tier.',
                'price_label' => 'PS49 one-off',
                'currency' => 'gbp',
                'amount_minor' => 4900,
                'billing_interval' => null,
                'monthly_credits' => null,
                'credits' => 100,
                'credits_label' => null,
                'stripe_product_id' => null,
                'stripe_price_id' => config('billing.top_ups.boost_100.stripe_price_id'),
                'is_active' => true,
                'sort_order' => 110,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'top_up',
                'key' => 'boost_250',
                'name' => '250-credit boost',
                'summary' => 'For bigger catalog pushes, agency work, or concentrated analysis windows.',
                'price_label' => 'PS99 one-off',
                'currency' => 'gbp',
                'amount_minor' => 9900,
                'billing_interval' => null,
                'monthly_credits' => null,
                'credits' => 250,
                'credits_label' => null,
                'stripe_product_id' => null,
                'stripe_price_id' => config('billing.top_ups.boost_250.stripe_price_id'),
                'is_active' => true,
                'sort_order' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_offers');
    }
};
