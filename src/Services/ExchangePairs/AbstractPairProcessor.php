<?php

namespace App\Services\ExchangePairs;

use App\DTO\CurrencyPairDTO;
use App\Services\ExchangePairs\Interfaces\PairProcessorInterface;
use App\Services\ExchangePairs\Interfaces\PairStorageInterface;

abstract class AbstractPairProcessor implements PairProcessorInterface
{
    // public function __construct(private PairStorageInterface $pairStorageService) {}

    public function processAllPairs(array $pairs): void
    {
        if (empty($pairs)) {
            echo "*** No currency pairs found.\n";
            return;
        }

        foreach ($pairs as $pair) {
            $this->processSinglePair($pair['from_currency'], $pair['to_currency']);
        }
    }

    abstract protected function processSinglePair(string $fromCurrency, string $toCurrency): bool;
}
