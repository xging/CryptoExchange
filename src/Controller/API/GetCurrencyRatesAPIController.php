<?php

namespace App\Controller\API;

use App\DTO\ExchangeRateAssert;
use App\DTO\ExchangeRateHistAssert;
use App\Repository\ExchangeRateHistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Services\CacheService;
use App\Repository\ExchangeRateRepository;
use Symfony\Component\HttpFoundation\Request;


#[Route('/api/currency')]
final class GetCurrencyRatesAPIController extends AbstractController
{
    public function __construct(
        private CacheService $cacheService,
        private ValidatorInterface $validator,
        private ExchangeRateRepository $exchangeRateRepository,
        private ExchangeRateHistRepository $exchangeRateHistRepository
    ) {}

    #[Route('/exchange-rate', name: 'get_currency_rate', methods: ['GET'])]
    public function showExchangeRate(Request $request): JsonResponse
    {

        $fromCurrency = $request->query->get('from');
        $toCurrency = $request->query->get('to');

        if (!$fromCurrency || !$toCurrency) {
            return $this->json(['error' => 'Missing required parameters: from, to'], Response::HTTP_BAD_REQUEST);
        }

        $currencyPairRequest = new ExchangeRateAssert($fromCurrency, $toCurrency);
        $errors = $this->validator->validate($currencyPairRequest);
        if (count($errors) > 0) {
            return $this->json(['errors' => array_map(fn($error) => $error->getMessage(), iterator_to_array($errors))], Response::HTTP_BAD_REQUEST);
        }

        $cacheKey = sprintf('CurrencyRate_%s_%s', $fromCurrency, $toCurrency);
        $exchangeRate = $this->cacheService->getOrSetCache(
            $cacheKey,
            fn() => $this->exchangeRateRepository->showRatesPair($fromCurrency, $toCurrency)
        );

        if (empty($exchangeRate)) {
            return $this->json(['error' => 'No exchange rate data found.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($exchangeRate);
    }

    #[Route('/exchange-rate-hist', name: 'get_currency_hist', methods: ['GET'])]
    public function showExchangeRateHistory(Request $request): JsonResponse
    {
        $fromCurrency = $request->query->get('from');
        $toCurrency = $request->query->get('to');
        $toDate = $request->query->get('date');
        $toTime = $request->query->get('time');

        if (!$fromCurrency || !$toCurrency) {
            return $this->json(['error' => 'Missing required parameters: from, to'], Response::HTTP_BAD_REQUEST);
        }

        $currencyPairRequest = new ExchangeRateHistAssert($fromCurrency, $toCurrency, $toDate, $toTime);

        $errors = $this->validator->validate($currencyPairRequest);
        if (count($errors) > 0) {
            return $this->json(['errors' => array_map(fn($error) => $error->getMessage(), iterator_to_array($errors))], Response::HTTP_BAD_REQUEST);
        }

        $cacheKeyParts = ["CurrencyRateHist", $fromCurrency, $toCurrency];
        if ($toDate) {
            $cacheKeyParts[] = str_replace('-', '', $toDate);
        }
        if ($toTime) {
            $cacheKeyParts[] = str_replace(':', '', $toTime);
        }
        $cacheKey = implode('_', $cacheKeyParts);

        $exchangeRateHistory = $this->cacheService->getOrSetCache(
            $cacheKey,
            fn() => $this->exchangeRateHistRepository->showRatesPairHist($fromCurrency, $toCurrency, $toDate, $toTime)
        );

        if (empty($exchangeRateHistory)) {
            return $this->json(['error' => 'No data found for the specified currency pair.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $exchangeRateHistory]);
    }
}
