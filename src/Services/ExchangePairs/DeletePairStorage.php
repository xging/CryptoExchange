<?php

namespace App\Services\ExchangePairs;

use App\Repository\CurrencyPairsRepository;
use App\Repository\ExchangeRateRepository;
use App\Services\ExchangePairs\Interfaces\DeletePairStorageInterface;

final class DeletePairStorage implements DeletePairStorageInterface
{
    public function __construct(
        private CurrencyPairsRepository $currencyPairRepository,
        private ExchangeRateRepository $exchangeRateRepository,
    ) {
    }

    public function deletePair(string $fromCurrency, string $toCurrency): bool
    {
        $this->exchangeRateRepository->deleteRates($fromCurrency, $toCurrency);

        return $this->currencyPairRepository->deleteCurrencyPair($fromCurrency, $toCurrency);
    }
}
