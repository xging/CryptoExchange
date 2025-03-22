<?php

namespace App\Services;

use App\Services\ExchangePairs\DeletePairProcessor;
use App\Services\Interfaces\CurrencyPairInterface;

final class DeleteCurrencyPairService implements CurrencyPairInterface
{
    public function __construct(private DeletePairProcessor $pairProcessorService)
    {
    }

    public function execute(array $args): void
    {
        echo "*** Deleting currency pairs from the queue.\n";
        $this->pairProcessorService->processAllPairs($args);
    }
}
