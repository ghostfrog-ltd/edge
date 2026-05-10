<?php

namespace App\Http\Controllers;

use App\Models\PricingOffer;
use App\Services\Billing\StripePricingSyncService;
use App\Support\BillingCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPlanController extends Controller
{
    public function __construct(protected BillingCatalog $catalog)
    {
    }

    public function index(): View
    {
        $offers = $this->catalog->allOffers();

        return view('admin.plans.index', [
            'plans' => $offers['plans'],
            'topUps' => $offers['topUps'],
        ]);
    }

    public function create(Request $request): View
    {
        $type = $request->query('type', 'plan');

        abort_unless(in_array($type, ['plan', 'top_up'], true), 404);

        return view('admin.plans.form', [
            'offer' => new PricingOffer([
                'type' => $type,
                'currency' => 'gbp',
                'billing_interval' => $type === 'plan' ? 'month' : null,
                'is_active' => true,
                'sort_order' => 999,
            ]),
            'formMode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        PricingOffer::create($data);

        return redirect()
            ->route('admin.plans.index')
            ->with('status', 'Pricing offer created.');
    }

    public function edit(PricingOffer $offer): View
    {
        return view('admin.plans.form', [
            'offer' => $offer,
            'formMode' => 'edit',
        ]);
    }

    public function update(Request $request, PricingOffer $offer): RedirectResponse
    {
        $offer->update($this->validatedData($request, $offer));

        return redirect()
            ->route('admin.plans.edit', $offer)
            ->with('status', 'Pricing offer updated.');
    }

    public function syncStripe(PricingOffer $offer, StripePricingSyncService $sync): RedirectResponse
    {
        try {
            $sync->sync($offer);
        } catch (\Throwable $exception) {
            return redirect()
                ->route('admin.plans.edit', $offer)
                ->withErrors([
                    'pricing' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('admin.plans.edit', $offer->fresh())
            ->with('status', 'Stripe product and price synced.');
    }

    protected function validatedData(Request $request, ?PricingOffer $offer = null): array
    {
        $validated = $request->validate([
            'type' => ['required', 'in:plan,top_up'],
            'key' => ['required', 'alpha_dash', 'max:255', Rule::unique('pricing_offers', 'key')->ignore($offer?->id)],
            'name' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string'],
            'price_label' => ['required', 'string', 'max:255'],
            'amount_display' => ['required', 'numeric', 'min:0'],
            'billing_interval' => ['nullable', 'in:month'],
            'monthly_credits' => ['nullable', 'integer', 'min:0'],
            'credits' => ['nullable', 'integer', 'min:0'],
            'credits_label' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'size:3'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $amountMinor = (int) round(((float) $validated['amount_display']) * 100);

        return [
            'type' => $validated['type'],
            'key' => $validated['key'],
            'name' => $validated['name'],
            'summary' => $validated['summary'],
            'price_label' => $validated['price_label'],
            'currency' => strtolower($validated['currency']),
            'amount_minor' => $amountMinor,
            'billing_interval' => $validated['type'] === 'plan' && $amountMinor > 0 ? ($validated['billing_interval'] ?: 'month') : null,
            'monthly_credits' => $validated['type'] === 'plan' ? ($validated['monthly_credits'] ?: 0) : null,
            'credits' => $validated['type'] === 'top_up' ? ($validated['credits'] ?: 0) : null,
            'credits_label' => $validated['type'] === 'plan' ? $validated['credits_label'] : null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'sort_order' => $validated['sort_order'],
        ];
    }
}
