<?php

namespace App\Services\Interfaces;

interface ExchangeRateHistInterface
{
    public function getRateHistory(string $from, string $to, ?string $date, ?string $time): ?array;
}
