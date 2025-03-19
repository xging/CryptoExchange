<?php

namespace App\Services\ExchangePairs;

use App\Services\ExchangePairs\Interfaces\WatchPairStorageInterface;
use App\Services\ExchangePairs\AbstractPairProcessor;

final class WatchPairProcessor extends AbstractPairProcessor
{
    public function __construct(private WatchPairStorageInterface $watchPairStorage) {}

    protected function processSinglePair(string $fromCurrency, string $toCurrency): bool
    {
        return $this->watchPairStorage->processExchangeRate($fromCurrency, $toCurrency);
    }
}
