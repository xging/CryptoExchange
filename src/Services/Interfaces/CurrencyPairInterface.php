<?php

namespace App\Services\Interfaces;

interface CurrencyPairInterface
{
    public function execute(array $args): void;
}
