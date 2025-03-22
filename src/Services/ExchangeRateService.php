<?php

namespace App\Services;

use App\Repository\ExchangeRateRepository;
use App\Services\Interfaces\ExchangeRateInterface;

final class ExchangeRateService implements ExchangeRateInterface
{
    public function __construct(
        private ExchangeRateRepository $repository,
        private CacheService $cache,
    ) {
    }

    public function getRate(string $from, string $to): ?array
    {
        $cacheKey = sprintf('CurrencyRate_%s_%s', $from, $to);

        return $this->cache->getOrSetCache(
            $cacheKey,
            fn () => $this->repository->showRatesPair($from, $to)
        );
    }
}
