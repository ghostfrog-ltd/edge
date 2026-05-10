<?php

namespace App\Services\Ebay;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class EbayApiClient
{
    public function hasCredentials(): bool
    {
        return filled(config('services.ebay.client_id')) && filled(config('services.ebay.client_secret'));
    }

    public function browseRequest(string $marketplace): PendingRequest
    {
        [$marketplaceId, $contentLanguage] = $this->marketplaceMeta($marketplace);

        return Http::acceptJson()
            ->withToken($this->accessToken())
            ->withHeaders([
                'X-EBAY-C-MARKETPLACE-ID' => $marketplaceId,
                'Accept-Language' => $contentLanguage,
            ])
            ->timeout(20);
    }

    public function taxonomyRequest(string $marketplace): PendingRequest
    {
        [$marketplaceId, $contentLanguage] = $this->marketplaceMeta($marketplace);

        return Http::acceptJson()
            ->withToken($this->accessToken())
            ->withHeaders([
                'X-EBAY-C-MARKETPLACE-ID' => $marketplaceId,
                'Accept-Language' => $contentLanguage,
            ])
            ->timeout(15);
    }

    public function marketplaceMeta(string $marketplace): array
    {
        return match ($marketplace) {
            'ebay-us' => ['EBAY_US', 'en-US'],
            'ebay-de' => ['EBAY_DE', 'de-DE'],
            default => ['EBAY_GB', 'en-GB'],
        };
    }

    public function defaultCategoryTreeId(string $marketplace): string
    {
        [$marketplaceId] = $this->marketplaceMeta($marketplace);

        return Cache::remember("ebay.default_tree_id.{$marketplaceId}", now()->addDay(), function () use ($marketplace, $marketplaceId): string {
            $response = $this->taxonomyRequest($marketplace)
                ->get(rtrim((string) config('services.ebay.taxonomy_url'), '/').'/get_default_category_tree_id', [
                    'marketplace_id' => $marketplaceId,
                ])
                ->throw()
                ->json();

            $treeId = (string) data_get($response, 'categoryTreeId');

            if ($treeId === '') {
                throw new RuntimeException('eBay Taxonomy API did not return a category tree id.');
            }

            return $treeId;
        });
    }

    public function accessToken(): string
    {
        if (! $this->hasCredentials()) {
            throw new RuntimeException('eBay credentials are not configured. Add EBAY_CLIENT_ID and EBAY_CLIENT_SECRET to .env.');
        }

        return Cache::remember('ebay.oauth_token', now()->addMinutes(110), function (): string {
            $response = Http::asForm()
                ->withBasicAuth(
                    (string) config('services.ebay.client_id'),
                    (string) config('services.ebay.client_secret')
                )
                ->timeout(15)
                ->post((string) config('services.ebay.oauth_url'), [
                    'grant_type' => 'client_credentials',
                    'scope' => 'https://api.ebay.com/oauth/api_scope',
                ])
                ->throw()
                ->json();

            $accessToken = (string) data_get($response, 'access_token');

            if ($accessToken === '') {
                throw new RuntimeException('eBay OAuth did not return an access token.');
            }

            return $accessToken;
        });
    }
}
