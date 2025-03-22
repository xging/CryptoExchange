<?php

namespace App\Services;

use Predis\Client;

final class CacheService
{
    public function __construct(private Client $redis)
    {
    }

    // Get or Set Redis cached data
    public function getOrSetCache(string $cacheKey, callable $callback, int $ttl = 300): mixed
    {
        $cachedData = $this->redis->get($cacheKey);

        if ($cachedData) {
            return json_decode($cachedData, true);
        }

        $data = $callback();
        if (!empty($data)) {
            $this->redis->setex($cacheKey, $ttl, json_encode($data));
        }

        return $data;
    }
}
