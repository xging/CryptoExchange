<?php

namespace App\Services\ExchangePairs\Interfaces;

interface AddPairStorageInterface
{
    public function saveIfNotExists(string $fromCurrency, string $toCurrency): bool;
}
