<?php

namespace App\Services\ExchangePairs;

use App\Entity\CurrencyPairs;
use App\Repository\CurrencyPairsRepository;
use App\Services\ExchangePairs\Interfaces\AddPairStorageInterface;

final class AddPairStorage implements AddPairStorageInterface
{
    public function __construct(private CurrencyPairsRepository $currencyPairRepository) {}

    public function saveIfNotExists(string $fromCurrency, string $toCurrency): bool
    {
        if ($this->currencyPairRepository->isPairRegistered($fromCurrency, $toCurrency)) {
            return false;
        }

        $currencyPair = (new CurrencyPairs())
            ->setFromCurrency($fromCurrency)
            ->setToCurrency($toCurrency)
            ->setCreationDate(new \DateTime());

        return $this->currencyPairRepository->saveCurrencyPair($currencyPair);
    }
}
