<?php

namespace App\Services\ExchangePairs\Interfaces;

interface DeletePairStorageInterface
{
    public function deletePair(string $from, string $to): bool;
}
