<?php

namespace App\Support;

class ScanKeywordGuard
{
    /**
     * Broad top-level keywords usually produce mushy reports. We can relax
     * this later when the engine learns how to decompose them into sub-families.
     */
    protected array $broadKeywords = [
        'apple',
        'samsung',
        'sony',
        'nike',
        'adidas',
        'lego',
        'pokemon',
        'xbox',
        'playstation',
        'nintendo',
        'furniture',
        'clothing',
        'trainers',
        'laptop',
        'phone',
        'tablet',
        'watch',
    ];

    public function isTooBroad(string $keyword): bool
    {
        $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $keyword) ?? ''));

        if ($normalized === '') {
            return false;
        }

        $tokens = preg_split('/\s+/', $normalized) ?: [];

        if (count($tokens) >= 2) {
            return false;
        }

        return in_array($normalized, $this->broadKeywords, true);
    }

    public function message(): string
    {
        return 'That search is too broad to produce a useful report yet. Try a more specific product or family such as "Apple iPad Pro 11", "iPhone 13 Pro", or "MacBook Air M2".';
    }
}
