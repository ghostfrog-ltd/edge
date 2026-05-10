<?php

namespace App\Services\Ebay;

use App\Models\Scan;
use App\Models\ScanEvidenceListing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class EbayListingEvidenceService
{
    public function __construct(protected EbayApiClient $api)
    {
    }

    public function syncTopListings(Scan $scan, int $limit = 50): int
    {
        $response = $this->api->browseRequest($scan->marketplace)
            ->get(rtrim((string) config('services.ebay.browse_url'), '/').'/item_summary/search', $this->query($scan, $limit))
            ->throw()
            ->json();

        $items = collect(data_get($response, 'itemSummaries', []))
            ->take($limit)
            ->values();

        if ($items->isEmpty()) {
            throw new RuntimeException('eBay returned no active listings for this scan.');
        }

        DB::transaction(function () use ($scan, $items): void {
            $scan->evidenceListings()->delete();

            $rows = $items->map(function (array $item, int $index) use ($scan): array {
                return [
                    'scan_id' => $scan->id,
                    'rank' => $index + 1,
                    'ebay_item_id' => (string) data_get($item, 'itemId'),
                    'title' => (string) data_get($item, 'title'),
                    'condition' => (string) (data_get($item, 'condition') ?: data_get($item, 'conditionId')),
                    'price_value' => data_get($item, 'price.value'),
                    'price_currency' => (string) data_get($item, 'price.currency'),
                    'item_web_url' => (string) data_get($item, 'itemWebUrl'),
                    'image_url' => (string) data_get($item, 'image.imageUrl'),
                    'seller_username' => (string) data_get($item, 'seller.username'),
                    'seller_feedback_percentage' => data_get($item, 'seller.feedbackPercentage'),
                    'seller_feedback_score' => data_get($item, 'seller.feedbackScore'),
                    'shipping_summary' => $this->shippingSummary($item),
                    'buying_options' => json_encode(array_values((array) data_get($item, 'buyingOptions', [])), JSON_THROW_ON_ERROR),
                    'category_id' => (string) (data_get($item, 'leafCategoryIds.0') ?: data_get($item, 'categories.0.categoryId')),
                    'category_name' => (string) data_get($item, 'categories.0.categoryName'),
                    'raw_payload' => json_encode($item, JSON_THROW_ON_ERROR),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all();

            ScanEvidenceListing::query()->insert($rows);
        });

        return $items->count();
    }

    protected function query(Scan $scan, int $limit): array
    {
        $query = [
            'q' => $scan->keyword,
            'limit' => min($limit, 50),
            'fieldgroups' => 'MATCHING_ITEMS',
            'auto_correct' => 'KEYWORD',
        ];

        if (filled($scan->ebay_category_id)) {
            $query['category_ids'] = $scan->ebay_category_id;
        }

        $filters = ['buyingOptions:{AUCTION|FIXED_PRICE}'];

        if ($seller = $this->sellerFilterFromUrl($scan->competitor_store_url)) {
            $filters[] = 'sellers:{'.$seller.'}';
        }

        $query['filter'] = implode(',', $filters);

        return $query;
    }

    protected function sellerFilterFromUrl(?string $url): ?string
    {
        if (! filled($url)) {
            return null;
        }

        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

        if ($path === '') {
            return null;
        }

        $segments = array_values(array_filter(explode('/', $path)));

        if (($segments[0] ?? null) === 'usr' && filled($segments[1] ?? null)) {
            return $this->sanitizeSeller((string) $segments[1]);
        }

        return null;
    }

    protected function sanitizeSeller(string $seller): ?string
    {
        $seller = trim($seller);

        if ($seller === '') {
            return null;
        }

        $seller = Str::of($seller)
            ->replace([' ', "\n", "\r", "\t"], '')
            ->toString();

        return $seller !== '' ? $seller : null;
    }

    protected function shippingSummary(array $item): ?string
    {
        $shipping = collect((array) data_get($item, 'shippingOptions', []))
            ->map(function (array $option): string {
                $type = (string) data_get($option, 'shippingCostType');
                $cost = trim(sprintf(
                    '%s %s',
                    (string) data_get($option, 'shippingCost.value'),
                    (string) data_get($option, 'shippingCost.currency')
                ));

                return trim($type.' '.$cost);
            })
            ->filter()
            ->implode(', ');

        return $shipping !== '' ? $shipping : null;
    }
}
