<?php

namespace App\Services;

use App\Services\ExchangePairs\AddPairProcessor;
use App\Services\Interfaces\CurrencyPairInterface;

final class AddCurrencyPairService implements CurrencyPairInterface
{
    public function __construct(private AddPairProcessor $pairProcessorService) {}

    public function execute(array $args): void
    {
        echo "*** Adding currency pairs into the queue.\n";
        $this->pairProcessorService->processAllPairs($args);
    }
}
