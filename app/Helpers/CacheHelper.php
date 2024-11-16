<?php

namespace App\Helpers;

use Cache;

class CacheHelper
{
    public function generateCacheKey(string $tag, ?string $key = null): string
    {
        return $key != null ? $tag.hash('sha256', $key) : $tag;
    }

    public function flushCache(string $tag, string $key): void
    {
        $cacheKey = $this->generateCacheKey($tag, $key);
        Cache::forget($cacheKey);
    }
}
