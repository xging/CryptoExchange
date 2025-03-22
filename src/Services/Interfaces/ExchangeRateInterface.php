<?php

namespace App\Services\Interfaces;

interface ExchangeRateInterface
{
    public function getRate(string $from, string $to): ?array;
}
