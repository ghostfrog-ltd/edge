<?php

namespace App\Services\Ebay;

use App\Models\Scan;
use App\Models\ScanEvidenceListing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EbaySchemaAuditService
{
    public function __construct(protected EbayApiClient $api)
    {
    }

    public function audit(Scan $scan, int $listingLimit = 50): array
    {
        $categoryId = $this->resolveCategoryId($scan);

        if (! $categoryId) {
            return [];
        }

        $aspectBlueprints = $this->preferredAspects($scan->marketplace, $categoryId);

        if ($aspectBlueprints->isEmpty()) {
            return [];
        }

        $listings = $scan->evidenceListings()->take($listingLimit)->get();

        if ($listings->isEmpty()) {
            return [];
        }

        $checkedListings = 0;
        $coverage = $aspectBlueprints->mapWithKeys(fn (array $aspect) => [
            mb_strtolower($aspect['aspect_name']) => 0,
        ]);

        foreach ($listings as $listing) {
            $aspects = $this->localizedAspects($scan->marketplace, $listing);

            if ($aspects->isEmpty()) {
                continue;
            }

            $checkedListings++;

            foreach ($aspects->keys() as $aspectName) {
                if ($coverage->has($aspectName)) {
                    $coverage[$aspectName] = $coverage[$aspectName] + 1;
                }
            }
        }

        if ($checkedListings === 0) {
            return [];
        }

        return $aspectBlueprints
            ->map(function (array $aspect) use ($coverage, $checkedListings): array {
                $key = mb_strtolower($aspect['aspect_name']);
                $presentCount = (int) ($coverage[$key] ?? 0);
                $missingCount = max($checkedListings - $presentCount, 0);
                $coveragePercent = (int) round(($presentCount / max($checkedListings, 1)) * 100);

                return [
                    'aspect_name' => $aspect['aspect_name'],
                    'requirement_level' => $aspect['requirement_level'],
                    'expected_required_by' => $aspect['expected_required_by'],
                    'present_count' => $presentCount,
                    'missing_count' => $missingCount,
                    'checked_listing_count' => $checkedListings,
                    'coverage_percent' => $coveragePercent,
                    'headline' => $this->headline(
                        $aspect['aspect_name'],
                        $aspect['requirement_level'],
                        $coveragePercent,
                        $checkedListings,
                        $aspect['expected_required_by']
                    ),
                ];
            })
            ->filter(fn (array $finding) => $finding['missing_count'] > 0)
            ->sortBy([
                fn (array $finding) => $this->priority($finding['requirement_level']),
                fn (array $finding) => -1 * $finding['missing_count'],
                fn (array $finding) => $finding['aspect_name'],
            ])
            ->take(5)
            ->values()
            ->all();
    }

    protected function resolveCategoryId(Scan $scan): ?string
    {
        if (filled($scan->ebay_category_id)) {
            return (string) $scan->ebay_category_id;
        }

        return $scan->evidenceListings()
            ->whereNotNull('category_id')
            ->select('category_id')
            ->groupBy('category_id')
            ->orderByRaw('count(*) desc')
            ->value('category_id');
    }

    protected function preferredAspects(string $marketplace, string $categoryId): Collection
    {
        $treeId = $this->api->defaultCategoryTreeId($marketplace);

        $response = $this->api->taxonomyRequest($marketplace)
            ->get(rtrim((string) config('services.ebay.taxonomy_url'), '/')."/category_tree/{$treeId}/get_item_aspects_for_category", [
                'category_id' => $categoryId,
            ])
            ->throw()
            ->json();

        return collect(data_get($response, 'aspects', []))
            ->map(function (array $aspect): ?array {
                $name = trim((string) data_get($aspect, 'localizedAspectName'));

                if ($name === '') {
                    return null;
                }

                $required = (bool) data_get($aspect, 'aspectConstraint.aspectRequired', false);
                $usage = strtoupper((string) data_get($aspect, 'aspectConstraint.aspectUsage', ''));
                $expectedRequiredBy = data_get($aspect, 'aspectConstraint.expectedRequiredByDate');

                $isPreferred = $required || $usage === 'RECOMMENDED' || filled($expectedRequiredBy);

                if (! $isPreferred) {
                    return null;
                }

                return [
                    'aspect_name' => $name,
                    'requirement_level' => $required
                        ? 'required'
                        : (filled($expectedRequiredBy) ? 'expected_required' : 'recommended'),
                    'expected_required_by' => $expectedRequiredBy,
                ];
            })
            ->filter()
            ->values();
    }

    protected function localizedAspects(string $marketplace, ScanEvidenceListing $listing): Collection
    {
        if (! filled($listing->ebay_item_id)) {
            return collect();
        }

        $cacheKey = 'ebay.item_aspects.'.md5($marketplace.'|'.$listing->ebay_item_id);

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($marketplace, $listing): Collection {
            $response = $this->api->browseRequest($marketplace)
                ->get(rtrim((string) config('services.ebay.browse_url'), '/').'/item/'.rawurlencode((string) $listing->ebay_item_id))
                ->throw()
                ->json();

            return collect(data_get($response, 'localizedAspects', []))
                ->mapWithKeys(function (array $aspect): array {
                    $name = mb_strtolower(trim((string) data_get($aspect, 'name')));

                    return $name !== '' ? [$name => (array) data_get($aspect, 'values', [])] : [];
                });
        });
    }

    protected function priority(string $requirementLevel): int
    {
        return match ($requirementLevel) {
            'required' => 0,
            'expected_required' => 1,
            default => 2,
        };
    }

    protected function headline(
        string $aspectName,
        string $requirementLevel,
        int $coveragePercent,
        int $checkedListings,
        ?string $expectedRequiredBy
    ): string {
        $levelLabel = match ($requirementLevel) {
            'required' => 'required by eBay',
            'expected_required' => 'likely to matter more soon',
            default => 'recommended by eBay',
        };

        $timing = $expectedRequiredBy ? ' eBay flags this with a timeline of '.$expectedRequiredBy.'.' : '';

        return sprintf(
            '%s is %s, but only %d%% of the top %d listings currently expose it.%s',
            $aspectName,
            $levelLabel,
            $coveragePercent,
            $checkedListings,
            $timing
        );
    }
}
