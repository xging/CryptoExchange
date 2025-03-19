<?php

namespace App\Services\ExchangePairs;

use App\Entity\ExchangeRate;
use App\Entity\ExchangeRateHist;
use App\Repository\ExchangeRateHistRepository;
use App\Repository\ExchangeRateRepository;
use App\Services\CurrencyRateExternalApiService;
use App\Services\ExchangePairs\Interfaces\WatchPairStorageInterface;

final class WatchPairStorage implements WatchPairStorageInterface
{
    public function __construct(
        private CurrencyRateExternalApiService $currencyRateApi,
        private ExchangeRateRepository $exchangeRateRepository,
        private ExchangeRateHistRepository $exchangeRateHistRepository
    ) {}

    public function processExchangeRate(string $fromCurrency, string $toCurrency): bool
    {
        $rate = $this->fetchRateOrFallback($fromCurrency, $toCurrency);

        if (!$this->rateExists($fromCurrency, $toCurrency)) {
            $exchangeRate = new ExchangeRate;
            $exchangeRate->setFromCurrency($fromCurrency)
                ->setToCurrency($toCurrency)
                ->setRate($rate)
                ->setCreationDate(new \DateTime());

            $this->handleNewRate($fromCurrency, $toCurrency, $rate, $exchangeRate);
            return true;
        } else {
            $this->handleExistingRate($fromCurrency, $toCurrency, $rate);
            return false;
        }
    }

    private function fetchRateOrFallback(string $fromCurrency, string $toCurrency): float
    {
        return $this->currencyRateApi->fetchExchangeRate($fromCurrency, $toCurrency) ?? 1.0;
    }

    private function rateExists(string $fromCurrency, string $toCurrency): bool
    {
        return $this->exchangeRateRepository->rateExists($fromCurrency, $toCurrency);
    }

    private function handleNewRate(string $fromCurrency, string $toCurrency, float $rate, ExchangeRate $exchangeRate): void
    {
        if ($rate === 0.0) {
            echo "*** Failed to save rate for: {$fromCurrency} -> {$toCurrency}\n";
            return;
        }

        if ($this->exchangeRateRepository->saveExchangeRate($exchangeRate)) {
            echo "*** Saved rate: {$fromCurrency} -> {$toCurrency}, rate: {$rate}\n";
        } else {
            echo "*** Failed to save rate for: {$fromCurrency} -> {$toCurrency}\n";
        }
    }

    private function handleExistingRate(string $fromCurrency, string $toCurrency, float $rate): void
    {
        $exchangeRateBeforeUpdate = $this->exchangeRateRepository->findOneBy([
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency
        ]);

        if (!$exchangeRateBeforeUpdate) {
            return;
        }

        $oldRate = $exchangeRateBeforeUpdate->getRate();

        if ($this->exchangeRateRepository->updateExchangeRate($fromCurrency, $toCurrency, $rate)) {
            echo "*** Updated rate: {$fromCurrency} -> {$toCurrency}, rate: {$rate}\n";

            $exchangeRateHist = new ExchangeRateHist;
            $exchangeRateHist->setFromCurrency($fromCurrency)
                ->setToCurrency($toCurrency)
                ->setOldRate($oldRate)
                ->setNewRate($rate)
                ->setCreationDate(new \DateTime())
                ->setLastUpdateDate(new \DateTime());

            $this->exchangeRateHistRepository->saveExchangeRateHist($exchangeRateHist);

            echo "*** Saved to history: {$fromCurrency} -> {$toCurrency}\n";
        } else {
            echo "*** Failed to update rate for: {$fromCurrency} -> {$toCurrency}\n";
        }
    }
}
