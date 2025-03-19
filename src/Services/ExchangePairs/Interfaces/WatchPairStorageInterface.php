<?php

namespace App\Services\ExchangePairs\Interfaces;

interface WatchPairStorageInterface
{
    public function processExchangeRate(string $from, string $to): bool;
}
