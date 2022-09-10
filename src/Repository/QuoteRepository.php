<?php

namespace App\Repository;

use App\Helper\SingletonTrait;
use App\Quote\Quote;

class QuoteRepository implements Repository
{
    use SingletonTrait;

    public function getById(int $id): Quote
    {
        // DO NOT MODIFY THIS METHOD
        $generator = \Faker\Factory::create();
        $generator->seed($id);

        return (new Quote())
            ->setId($id)
            ->setSiteId($generator->numberBetween(1, 10))
            ->setDestinationId($generator->numberBetween(1, 200))
            ->setDateQuoted(new \DateTime());
    }
}
