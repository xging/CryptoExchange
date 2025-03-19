<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ExchangeRateHistAssert
{
    #[Assert\Length(min: 3, max: 10, minMessage: "From currency must be exactly 3 letters", maxMessage: "From currency must be exactly 3 letters")]
    #[Assert\Regex("/^[A-Za-z]+$/", message: "From currency must contain only letters")]
    public string $fromCurrency;

    #[Assert\Length(min: 3, max: 3, minMessage: "To currency must be exactly 3 letters", maxMessage: "To currency must be exactly 3 letters")]
    #[Assert\Regex("/^[A-Za-z]+$/", message: "To currency must contain only letters")]
    public string $toCurrency;

    #[Assert\Date(message: "Invalid date format")]
    public ?string $toDate;

    #[Assert\Time(message: "Invalid time format")]
    public ?string $toTime;

    public function __construct(string $fromCurrency, string $toCurrency, ?string $toDate = null, ?string $toTime = null)
    {
        $this->fromCurrency = $fromCurrency;
        $this->toCurrency = $toCurrency;
        $this->toDate = $toDate;
        $this->toTime = $toTime;
    }
}
