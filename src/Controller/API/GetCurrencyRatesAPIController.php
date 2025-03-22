<?php

namespace App\Controller\API;

use App\DTO\ExchangeRateAssert;
use App\DTO\ExchangeRateHistAssert;
use App\Services\Interfaces\ExchangeRateHistInterface;
use App\Services\Interfaces\ExchangeRateInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/currency')]
final class GetCurrencyRatesAPIController extends AbstractController
{
    public function __construct(
        private ExchangeRateInterface $exchangeRateService,
        private ExchangeRateHistInterface $exchangeRateHistoryService,
        private ValidatorInterface $validator,
    ) {
    }

    #[Route('/exchange-rate', name: 'get_currency_rate', methods: ['GET'])]
    public function showExchangeRate(Request $request): JsonResponse
    {
        $from = $request->query->get('from');
        $to   = $request->query->get('to');

        if (!$from || !$to) {
            return $this->missingParamsResponse(['from', 'to']);
        }

        $dto = new ExchangeRateAssert($from, $to);
        if ($error = $this->validateDto($dto)) {
            return $error;
        }

        $rate = $this->exchangeRateService->getRate($from, $to);

        return $rate
            ? $this->json($rate)
            : $this->json(['error' => 'No data found.'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/exchange-rate-hist', name: 'get_currency_hist', methods: ['GET'])]
    public function showExchangeRateHistory(Request $request): JsonResponse
    {
        $from = $request->query->get('from');
        $to   = $request->query->get('to');
        $date = $request->query->get('date');
        $time = $request->query->get('time');

        if (!$from || !$to) {
            return $this->missingParamsResponse(['from', 'to']);
        }

        $dto = new ExchangeRateHistAssert($from, $to, $date, $time);
        if ($error = $this->validateDto($dto)) {
            return $error;
        }

        $history = $this->exchangeRateHistoryService->getRateHistory($from, $to, $date, $time);

        return $history
            ? $this->json(['data' => $history])
            : $this->json(['error' => 'No historical data found.'], Response::HTTP_NOT_FOUND);
    }

    private function missingParamsResponse(array $required): JsonResponse
    {
        return $this->json([
            'error' => 'Missing required parameters: '.implode(', ', $required),
        ], Response::HTTP_BAD_REQUEST);
    }

    private function validateDto(object $dto): ?JsonResponse
    {
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json([
                'errors' => array_map(fn ($e) => $e->getMessage(), iterator_to_array($errors)),
            ], Response::HTTP_BAD_REQUEST);
        }

        return null;
    }
}
