<?php

namespace App\Services;

use App\Repository\CurrencyPairsRepository;
use App\Services\ExchangePairs\WatchPairProcessor;
use App\Services\Interfaces\CurrencyPairInterface;

final class WatchCurrencyPairService implements CurrencyPairInterface
{
    public function __construct(
        private WatchPairProcessor $pairProcessorService,
        private CurrencyPairsRepository $currencyPairs,
    ) {
    }

    public function execute(?array $args = null): void
    {
        echo "*** Watch currency pairs from the queue.\n";
        $pairs = $this->currencyPairs->getAllCurrencyPairs();
        $this->pairProcessorService->processAllPairs($pairs);
    }
}
