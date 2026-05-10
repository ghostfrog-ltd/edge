<?php

namespace App\Services\Ebay;

use RuntimeException;

class EbayCategorySuggestionService
{
    public function __construct(protected EbayApiClient $api)
    {
    }

    public function suggestions(string $keyword, string $marketplace): array
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return [];
        }

        $treeId = $this->defaultCategoryTreeId($marketplace);

        $response = $this->api->taxonomyRequest($marketplace)
            ->get(rtrim((string) config('services.ebay.taxonomy_url'), '/')."/category_tree/{$treeId}/get_category_suggestions", [
                'q' => $keyword,
            ])
            ->throw()
            ->json();

        return collect(data_get($response, 'categorySuggestions', []))
            ->map(fn (array $suggestion) => [
                'id' => (string) data_get($suggestion, 'category.categoryId'),
                'name' => (string) data_get($suggestion, 'category.categoryName'),
                'tree_id' => (string) $treeId,
                'label' => trim(sprintf(
                    '%s (%s)',
                    (string) data_get($suggestion, 'category.categoryName'),
                    (string) data_get($suggestion, 'category.categoryId')
                )),
            ])
            ->filter(fn (array $suggestion) => $suggestion['id'] !== '' && $suggestion['name'] !== '')
            ->values()
            ->all();
    }

    public function hasCredentials(): bool
    {
        return $this->api->hasCredentials();
    }

    protected function accessToken(): string
    {
        return $this->api->accessToken();
    }

    protected function defaultCategoryTreeId(string $marketplace): string
    {
        return $this->api->defaultCategoryTreeId($marketplace);
    }
}
