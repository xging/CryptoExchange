<?php

namespace App\Repository;

use App\Entity\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<ExchangeRate>
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $em)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    public function showRatesPair(string $fromCurrency, string $toCurrency): array
    {
        $rates = $this->em->getRepository(ExchangeRate::class)
            ->findBy(
                ['from_currency' => $fromCurrency, 'to_currency' => $toCurrency],
                ['id' => 'DESC']
            );

        return array_map(fn($rate) => [
            'from_currency' => $rate->getFromCurrency(),
            'to_currency' => $rate->getToCurrency(),
            'rate' => $rate->getRate(),
        ], $rates);
    }


    public function rateExists(string $fromCurrency, string $toCurrency): bool
    {
        return $this->em->getRepository(ExchangeRate::class)
            ->count(['from_currency' => $fromCurrency, 'to_currency' => $toCurrency]) > 0;
    }


    public function updateExchangeRate(string $fromCurrency, string $toCurrency, float $newRate): bool
    {
        $exchangeRate = $this->findOneBy(['from_currency' => $fromCurrency, 'to_currency' => $toCurrency]);

        if (!$exchangeRate) {
            return false;
        }

        // $oldRate = $exchangeRate->getRate();
        // if ($oldRate === $newRate) {
        //     return false;
        // }

        $exchangeRate->setRate($newRate)
            ->setLastUpdateDate(new \DateTime());

        $this->em->flush();

        return true;
    }

    public function deleteRates(string $fromCurrency, string $toCurrency): bool
    {
        $exchangeRate = $this->em->getRepository(ExchangeRate::class)
            ->findOneBy(['from_currency' => $fromCurrency, 'to_currency' => $toCurrency]);

        if ($exchangeRate) {
            $this->em->remove($exchangeRate);
            $this->em->flush();
            return true;
        }
        return false;
    }

    public function saveExchangeRate(ExchangeRate $exchangeRate): bool
    {
        try {
            $this->em->persist($exchangeRate);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error saving currency pairs: " . $e->getMessage(), 0, $e);
        }
        return true;
    }
}
