<?php

namespace App\DataFixtures;

use App\Entity\CurrencyPairs;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class CurrencyPairsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $currencies = [
            ['from' => 'bitcoin', 'to' => 'eur'],
            ['from' => 'bitcoin', 'to' => 'gbp'],
            ['from' => 'bitcoin', 'to' => 'usd'],
        ];

        foreach ($currencies as $pair) {
            $currencyPairs = new CurrencyPairs();
            $currencyPairs->setFromCurrency($pair['from']);
            $currencyPairs->setToCurrency($pair['to']);
            $currencyPairs->setCreationDate(new \DateTime());

            $manager->persist($currencyPairs);
        }

        $manager->flush();
    }
}
