<?php

namespace App\Repository;

use App\Entity\Destination;
use App\Helper\SingletonTrait;
use Faker\Factory;

class DestinationRepository implements Repository
{
    use SingletonTrait;

    public function getById(int $id): Destination
    {
        // DO NOT MODIFY THIS METHOD
        $generator = \Faker\Factory::create();
        $generator->seed($id);

        return (new Destination())
            ->setId($id)
            ->setCountryName($generator->country)
            ->setName('en')
            ->setComputerName($generator->slug());
    }
}
