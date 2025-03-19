<?php

namespace App\Services\ExchangePairs\Interfaces;

interface PairProcessorInterface
{
    public function processAllPairs(array $pairs): void;
}
