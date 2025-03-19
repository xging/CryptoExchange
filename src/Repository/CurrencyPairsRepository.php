<?php

namespace App\Repository;

use App\Entity\CurrencyPairs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<CurrencyPairs>
 */
class CurrencyPairsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $em)
    {
        parent::__construct($registry, CurrencyPairs::class);
    }





    public function isPairRegistered(string $fromCurrency, string $toCurrency): bool
    {
        $currencyPair = $this->em->getRepository(CurrencyPairs::class)
            ->findOneBy(['from_currency' => $fromCurrency, 'to_currency' => $toCurrency]);
        return $currencyPair !== null;
    }



    // public function checkIfPairExistsArray(): array
    // {
    //     $currencyPairs = $this->em->getRepository(CurrencyPairs::class)
    //         ->findAll();
    //     if ($currencyPairs) {
    //         return array_map(function ($currencyPair) {
    //             return [
    //                 'from_currency' => $currencyPair->getFromCurrency(),
    //                 'to_currency' => $currencyPair->getToCurrency(),
    //             ];
    //         }, $currencyPairs);
    //     }
    //     return [];
    // }


    public function getAllCurrencyPairs(): array
    {
        return array_map(fn(CurrencyPairs $pair) => [
            'from_currency' => $pair->getFromCurrency(),
            'to_currency' => $pair->getToCurrency(),
        ], $this->findAll());
    }

    public function deleteCurrencyPair(string $fromCurrency, string $toCurrency): bool
    {
        $currencyPair = $this->em->getRepository(CurrencyPairs::class)
            ->findOneBy(['from_currency' => $fromCurrency, 'to_currency' => $toCurrency]);

        if ($currencyPair) {
            $this->em->remove($currencyPair);
            $this->em->flush();
            return true;
        }

        return false;
    }

    public function saveCurrencyPair(CurrencyPairs $currencyPair): bool
    {
        try {
            $this->em->persist($currencyPair);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error saving currency pairs: " . $e->getMessage(), 0, $e);
        }
        return true;
    }
}
