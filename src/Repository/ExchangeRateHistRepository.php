<?php

namespace App\Repository;

use App\Entity\ExchangeRateHist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<ExchangeRateHist>
 */
class ExchangeRateHistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $em)
    {
        parent::__construct($registry, ExchangeRateHist::class);
    }


    public function showRatesPairHist(string $fromCurrency, string $toCurrency, ?string $date, ?string $time): array
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('e')
            ->from(ExchangeRateHist::class, 'e')
            ->where('e.from_currency = :from_currency')
            ->andWhere('e.to_currency = :to_currency')
            ->setParameter('from_currency', $fromCurrency)
            ->setParameter('to_currency', $toCurrency)
            ->orderBy('e.id', 'DESC');

        if ($date !== null) {
            $startOfDay = new \DateTimeImmutable($date . ' 00:00:00');
            $endOfDay = new \DateTimeImmutable($date . ' 23:59:59');

            if ($time !== null) {
                $dateTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time);

                if (!$dateTime) {
                    throw new \InvalidArgumentException('Invalid date or time format.');
                }

                $qb->andWhere('e.creation_date = :creation_date')
                    ->setParameter('creation_date', $dateTime);
            } else {
                $qb->andWhere('e.creation_date BETWEEN :start AND :end')
                    ->setParameter('start', $startOfDay)
                    ->setParameter('end', $endOfDay);
            }
        }

        $rates = $qb->getQuery()->getResult();

        return array_map(fn($rate) => [
            'from_currency' => $rate->getFromCurrency(),
            'to_currency' => $rate->getToCurrency(),
            'old_rate' => $rate->getOldRate(),
            'last_rate' => $rate->getNewRate(),
            'update_date' => $rate->getLastUpdateDate()?->format('Y-m-d H:i:s'),
            'creation_date' => $rate->getCreationDate()?->format('Y-m-d H:i:s'),
        ], $rates);
    }


    public function saveExchangeRateHist(ExchangeRateHist $exchangeRateHist): bool
    {
        try {
            $this->em->persist($exchangeRateHist);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error saving history: " . $e->getMessage(), 0, $e);
        }
        return true;
    }
}
