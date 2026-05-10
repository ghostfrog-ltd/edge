<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Support\BillingCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Laravel\Cashier\Subscription;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Stripe;
use Throwable;

class BillingController extends Controller
{
    public function __construct(protected BillingCatalog $catalog)
    {
    }

    public function index(Request $request): View
    {
        $team = $request->user()->currentTeam;

        abort_unless($team, 403);

        $team->load('subscriptions');

        $subscription = $team->subscription('default');
        $activePlan = $this->catalog->planForPrice($subscription?->stripe_price);

        return view('billing.index', [
            'team' => $team,
            'plans' => $this->catalog->plans(),
            'topUps' => $this->catalog->topUps(),
            'subscription' => $subscription,
            'activePlan' => $activePlan,
            'checkoutFeedback' => $this->checkoutFeedback($request, $team, $subscription, $activePlan),
            'stripeConfigured' => filled(config('cashier.secret')),
        ]);
    }

    public function subscribe(Request $request, string $plan): mixed
    {
        $team = $request->user()->currentTeam;

        abort_unless($team, 403);

        $planData = $this->catalog->plan($plan);

        if (! $planData || ! filled($planData['stripe_price_id'] ?? null)) {
            return back()->withErrors([
                'billing' => 'That plan is not configured with a Stripe price ID yet.',
            ]);
        }

        if (! filled(config('cashier.secret'))) {
            return back()->withErrors([
                'billing' => 'Stripe secret key is missing from the app configuration.',
            ]);
        }

        if ($team->subscribed('default')) {
            return redirect()
                ->route('billing.index')
                ->with('status', 'This workspace already has an active subscription. Use the billing portal to manage it.');
        }

        return $team->newSubscription('default', $planData['stripe_price_id'])->checkout([
            'success_url' => route('billing.index', ['checkout' => 'success']).'&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('billing.index', ['checkout' => 'cancelled']),
        ]);
    }

    public function topUp(Request $request, string $pack): mixed
    {
        $team = $request->user()->currentTeam;

        abort_unless($team, 403);

        $packData = $this->catalog->topUp($pack);

        if (! $packData || ! filled($packData['stripe_price_id'] ?? null)) {
            return back()->withErrors([
                'billing' => 'That top-up pack is not configured with a Stripe price ID yet.',
            ]);
        }

        if (! filled(config('cashier.secret'))) {
            return back()->withErrors([
                'billing' => 'Stripe secret key is missing from the app configuration.',
            ]);
        }

        return $team->checkout([
            [
                'price' => $packData['stripe_price_id'],
                'quantity' => 1,
            ],
        ], [
            'success_url' => route('billing.index', ['checkout' => 'success']).'&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('billing.index', ['checkout' => 'cancelled']),
            'metadata' => [
                'ghostfrog_type' => 'credit_top_up',
                'top_up_key' => $packData['key'],
                'credits' => (string) $packData['credits'],
                'team_id' => (string) $team->id,
            ],
        ]);
    }

    public function portal(Request $request): RedirectResponse
    {
        $team = $request->user()->currentTeam;

        abort_unless($team, 403);

        if (! filled($team->stripe_id)) {
            return redirect()
                ->route('billing.index')
                ->withErrors([
                    'billing' => 'This workspace does not have a Stripe customer record yet. Start a checkout first.',
                ]);
        }

        return $team->redirectToBillingPortal(route('billing.index'));
    }

    protected function checkoutFeedback(Request $request, Team $team, ?Subscription $subscription, ?array $activePlan): ?array
    {
        $checkoutState = $request->query('checkout');

        if ($checkoutState === 'cancelled') {
            return [
                'tone' => 'cancelled',
                'message' => 'Checkout was cancelled. Nothing changed on this workspace.',
            ];
        }

        if ($checkoutState !== 'success') {
            return null;
        }

        $sessionId = (string) $request->query('session_id', '');

        if (filled($sessionId) && ($feedback = $this->checkoutFeedbackFromSession($team, $sessionId, $subscription, $activePlan))) {
            return $feedback;
        }

        if ($subscription?->valid()) {
            return [
                'tone' => 'success',
                'message' => sprintf(
                    '%s subscription is active on this workspace.',
                    $activePlan['name'] ?? 'Your'
                ),
            ];
        }

        if ($team->creditLedgers()->where('type', 'stripe_top_up')->exists()) {
            return [
                'tone' => 'success',
                'message' => 'Your credit top-up has been added to this workspace.',
            ];
        }

        return [
            'tone' => 'pending',
            'message' => 'Stripe checkout completed. Subscription status and credit top-ups will settle here once the Stripe webhook lands.',
        ];
    }

    protected function checkoutFeedbackFromSession(Team $team, string $sessionId, ?Subscription $subscription, ?array $activePlan): ?array
    {
        if (! filled(config('cashier.secret'))) {
            return null;
        }

        try {
            Stripe::setApiKey(config('cashier.secret'));

            $session = StripeCheckoutSession::retrieve($sessionId);
        } catch (Throwable $exception) {
            Log::warning('Unable to retrieve Stripe checkout session for billing feedback.', [
                'session_id' => $sessionId,
                'team_id' => $team->id,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }

        if (($session->customer ?? null) !== $team->stripe_id) {
            return null;
        }

        if (($session->mode ?? null) === 'subscription') {
            $settled = filled($session->subscription ?? null)
                ? $team->subscriptions()->where('stripe_id', $session->subscription)->exists()
                : (bool) $subscription?->valid();

            if ($settled) {
                return [
                    'tone' => 'success',
                    'message' => sprintf(
                        '%s subscription is active on this workspace.',
                        $activePlan['name'] ?? 'Your'
                    ),
                ];
            }
        }

        if (($session->mode ?? null) === 'payment' && data_get($session, 'metadata.ghostfrog_type') === 'credit_top_up') {
            $reference = 'stripe_checkout:'.$sessionId;
            $topUpLedger = $team->creditLedgers()->where('reference', $reference)->first();

            if ($topUpLedger) {
                return [
                    'tone' => 'success',
                    'message' => sprintf(
                        '%d credits were added to this workspace.',
                        max(0, (int) $topUpLedger->amount)
                    ),
                ];
            }
        }

        return null;
    }
}
