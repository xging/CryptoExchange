<?php

namespace App\Services\ExchangePairs;

use App\Services\ExchangePairs\Interfaces\AddPairStorageInterface;

final class AddPairProcessor extends AbstractPairProcessor
{
    public function __construct(private AddPairStorageInterface $pairStorageService) {}

    protected function processSinglePair(string $fromCurrency, string $toCurrency): bool
    {
        return $this->pairStorageService->saveIfNotExists($fromCurrency, $toCurrency);
    }
}
