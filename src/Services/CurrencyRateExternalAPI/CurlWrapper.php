<?php

namespace App\Services\CurrencyRateExternalAPI;

final class CurlWrapper
{
    public function exec($ch)
    {
        return curl_exec($ch);
    }

    public function error($ch)
    {
        return curl_error($ch);
    }

    public function close($ch)
    {
        curl_close($ch);
    }
}
