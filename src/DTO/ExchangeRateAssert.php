<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ExchangeRateAssert
{
    #[Assert\Length(min: 3, max: 10, minMessage: 'From currency must be exactly 3 letters', maxMessage: 'From currency must be exactly 3 letters')]
    #[Assert\Regex('/^[A-Za-z]+$/', message: 'From currency must contain only letters')]
    public string $fromCurrency;

    #[Assert\Length(min: 3, max: 3, minMessage: 'To currency must be exactly 3 letters', maxMessage: 'To currency must be exactly 3 letters')]
    #[Assert\Regex('/^[A-Za-z]+$/', message: 'To currency must contain only letters')]
    public string $toCurrency;

    public function __construct(string $fromCurrency, string $toCurrency)
    {
        $this->fromCurrency = $fromCurrency;
        $this->toCurrency   = $toCurrency;
    }
}
