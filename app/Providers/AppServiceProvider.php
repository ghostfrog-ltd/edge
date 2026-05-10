<?php

namespace App\Providers;

use App\Models\Team;
use App\Models\User;
use App\Services\Billing\TeamBillingFulfillmentService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookReceived;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cashier::useCustomerModel(Team::class);

        Event::listen(WebhookReceived::class, function (WebhookReceived $event): void {
            app(TeamBillingFulfillmentService::class)->handleWebhook($event->payload);
        });

        Gate::define('access-admin', fn (User $user) => (bool) $user->is_admin);
    }
}
