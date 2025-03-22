<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CurrencyRateExternalApiService
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public function fetchExchangeRate(string $from, string $to): ?float
    {
        usleep(2_000_000);
        $crypto = strtolower($from);
        $fiat   = strtolower($to);

        $url = "https://api.coingecko.com/api/v3/simple/price?ids={$crypto}&vs_currencies={$fiat}";

        try {
            $response = $this->httpClient->request('GET', $url, [
                'timeout' => 10,
            ]);

            $data = $response->toArray();

            if (isset($data[$crypto][$fiat])) {
                return (float) $data[$crypto][$fiat];
            }
        } catch (\Throwable $e) {
            echo $e->getMessage();

            return null;
        }

        return null;
    }
}
