<?php

namespace App\Repository;

use App\Entity\Site;
use App\Helper\SingletonTrait;
use Faker\Factory;

class SiteRepository implements Repository
{
    use SingletonTrait;

    public function getById(int $id): Site
    {
        // DO NOT MODIFY THIS METHOD
        $generator = \Faker\Factory::create();
        $generator->seed($id);

        return (new Site())->setId($id)->setUrl($generator->url);
    }
}
