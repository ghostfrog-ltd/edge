<?php

namespace App\Services\Ebay;

use App\Models\Scan;
use App\Models\ScanEvidenceListing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EbayIntelligenceGatheringService
{
    protected array $signals = [
        'battery_cycle_count' => [
            'patterns' => ['cycle count', 'battery cycle', 'battery cycles'],
            'headline' => 'Battery cycle count keeps showing up in competitor detail text, which suggests buyers are actively looking for a stronger longevity signal.',
            'action' => 'Add battery cycle count or the closest battery-health equivalent high in the listing so buyers do not have to ask.',
        ],
        'battery_health' => [
            'patterns' => ['battery health', 'battery percentage', 'battery %', 'battery tested', 'battery condition'],
            'headline' => 'Battery health comes up repeatedly in competitor copy, which is a strong clue that buyer confidence depends on it in this niche.',
            'action' => 'Make battery health explicit in item specifics and the first description block.',
        ],
        'original_charger' => [
            'patterns' => ['original charger', 'genuine charger', 'official charger', 'charger included', 'charger not included', 'no charger', 'without charger', 'usb cable'],
            'headline' => 'Charger inclusion and originality are recurring reassurance points in competitor descriptions, which means buyers care about what arrives in the box.',
            'action' => 'State whether the charger is original, third-party, or missing in a dedicated bullet and item specific.',
        ],
        'screen_condition' => [
            'patterns' => ['screen condition', 'white spot', 'screen burn', 'screen bleed', 'screen scratch', 'display condition', 'fully working screen', 'no screen cracks', 'minor scratches'],
            'headline' => 'Competitors repeatedly defend screen quality in their descriptions, which usually means buyers are nervous about hidden display defects.',
            'action' => 'Add a clearer display-condition note and a proof photo that answers the screen-quality question immediately.',
        ],
        'network_lock' => [
            'patterns' => ['unlocked', 'network locked', 'sim free', 'sim-free', 'icloud', 'carrier', 'wifi only', 'wi-fi only', 'cellular', '4g', '5g'],
            'headline' => 'Lock status appears often in competitor detail text, which suggests buyers are trying to avoid compatibility surprises.',
            'action' => 'Call out network lock, carrier state, and any account locks early in the title and item specifics.',
        ],
        'accessories' => [
            'patterns' => ['apple pencil', 'case included', 'no box', 'accessories', 'cable included', 'stylus', 'boxed', 'box only', 'includes case'],
            'headline' => 'Accessory inclusion keeps being clarified by competitors, which is a sign buyers want the contents of the bundle spelled out before purchase.',
            'action' => 'List included and missing accessories as separate bullets so there is no uncertainty about the package.',
        ],
        'storage_connectivity' => [
            'patterns' => ['32gb', '64gb', '128gb', '256gb', '512gb', '1tb', 'wifi', 'wi-fi', 'cellular'],
            'headline' => 'Storage and connectivity variants are being repeated heavily by competitors, which suggests buyers are filtering quickly and need the exact spec up front.',
            'action' => 'Push storage size and Wi-Fi versus cellular status into the title and item specifics so the variant is obvious at a glance.',
        ],
        'fitment' => [
            'patterns' => ['fitment', 'fits', 'compatible with', 'compatibility', 'part number'],
            'headline' => 'Compatibility language is repeated heavily across competitor listings, which points to buyers needing stronger reassurance before buying.',
            'action' => 'Expand fitment years, part numbers, and compatibility notes so buyers can self-verify the part quickly.',
        ],
        'wear' => [
            'patterns' => ['wear', 'spline', 'teeth', 'mileage', 'donor bike', 'rounding'],
            'headline' => 'Competitors keep addressing wear and donor-condition details, which suggests buyers are trying to judge the usable life of the part.',
            'action' => 'Surface donor mileage, visible wear points, and close-up proof photos earlier in the listing.',
        ],
    ];

    public function __construct(protected EbayApiClient $api)
    {
    }

    public function gather(Scan $scan, int $listingLimit = 50): array
    {
        $listings = $scan->evidenceListings()->take($listingLimit)->get();

        if ($listings->isEmpty()) {
            return [];
        }

        $hitCounts = collect(array_keys($this->signals))->mapWithKeys(fn (string $key) => [$key => 0]);
        $checkedListings = 0;

        foreach ($listings as $listing) {
            $detailText = $this->detailText($scan->marketplace, $listing);

            if ($detailText === '') {
                continue;
            }

            $checkedListings++;

            foreach ($this->signals as $key => $signal) {
                if ($this->matches($detailText, $signal['patterns'])) {
                    $hitCounts[$key] = $hitCounts[$key] + 1;
                }
            }
        }

        if ($checkedListings === 0) {
            return [];
        }

        return collect($this->signals)
            ->map(function (array $signal, string $key) use ($hitCounts, $checkedListings): ?array {
                $count = (int) ($hitCounts[$key] ?? 0);

                if ($count < 2) {
                    return null;
                }

                return [
                    'signal_key' => $key,
                    'headline' => $signal['headline'],
                    'action' => $signal['action'],
                    'mention_count' => $count,
                    'checked_listing_count' => $checkedListings,
                    'coverage_percent' => (int) round(($count / max($checkedListings, 1)) * 100),
                ];
            })
            ->filter()
            ->sortByDesc('mention_count')
            ->take(3)
            ->values()
            ->all();
    }

    protected function detailText(string $marketplace, ScanEvidenceListing $listing): string
    {
        if (! filled($listing->ebay_item_id)) {
            return '';
        }

        $cacheKey = 'ebay.item_intelligence.'.md5($marketplace.'|'.$listing->ebay_item_id);

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($marketplace, $listing): string {
            $response = $this->api->browseRequest($marketplace)
                ->get(rtrim((string) config('services.ebay.browse_url'), '/').'/item/'.rawurlencode((string) $listing->ebay_item_id))
                ->throw()
                ->json();

            $parts = array_filter([
                (string) data_get($response, 'title'),
                (string) data_get($response, 'shortDescription'),
                (string) data_get($response, 'description'),
                (string) data_get($response, 'conditionDescriptor.additionalInfo'),
            ]);

            $text = strtolower(trim(strip_tags(implode(' ', $parts))));
            $text = preg_replace('/\s+/', ' ', $text ?? '');

            return is_string($text) ? $text : '';
        });
    }

    protected function matches(string $text, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (str_contains($text, strtolower($pattern))) {
                return true;
            }
        }

        return false;
    }
}
