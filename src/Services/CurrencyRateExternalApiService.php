<?php

namespace App\Services;

use App\Services\CurrencyRateExternalAPI\CurlWrapper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class CurrencyRateExternalApiService
{

    public function __construct(private CurlWrapper $curl, private ParameterBagInterface $params) {}

    public function fetchExchangeRate(string $from, string $to): ?float
    {
        $crypto = strtolower($from);
        $fiat = strtolower($to);

        $url = "https://api.coingecko.com/api/v3/simple/price?ids={$crypto}&vs_currencies={$fiat}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = $this->curl->exec($ch);

        if ($response === false) {
            $error = $this->curl->error($ch);
            $this->curl->close($ch);
            return null;
        }

        $this->curl->close($ch);

        $data = json_decode($response, true);

        if (isset($data[$crypto][$fiat])) {
            return (float) $data[$crypto][$fiat];
        }

        return null;
    }
}
