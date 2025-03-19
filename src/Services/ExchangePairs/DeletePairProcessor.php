<?php

namespace App\Services\ExchangePairs;

use App\Services\ExchangePairs\Interfaces\DeletePairStorageInterface;
use App\Services\ExchangePairs\AbstractPairProcessor;


final class DeletePairProcessor extends AbstractPairProcessor
{
    public function __construct(private DeletePairStorageInterface $pairStorageService) {}

    protected function processSinglePair(string $fromCurrency, string $toCurrency): bool
    {
        return $this->pairStorageService->deletePair($fromCurrency, $toCurrency);
    }
}
