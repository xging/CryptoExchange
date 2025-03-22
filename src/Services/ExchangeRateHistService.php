<?php

namespace App\Services;

use App\Repository\ExchangeRateHistRepository;
use App\Services\Interfaces\ExchangeRateHistInterface;

final class ExchangeRateHistService implements ExchangeRateHistInterface
{
    public function __construct(
        private ExchangeRateHistRepository $repository,
        private CacheService $cache,
    ) {
    }

    public function getRateHistory(string $from, string $to, ?string $date, ?string $time): ?array
    {
        $parts = ['CurrencyRateHist', $from, $to];

        if ($date) {
            $parts[] = str_replace('-', '', $date);
        }

        if ($time) {
            $parts[] = str_replace(':', '', $time);
        }

        $cacheKey = implode('_', $parts);

        return $this->cache->getOrSetCache(
            $cacheKey,
            fn () => $this->repository->showRatesPairHist($from, $to, $date, $time)
        );
    }
}
