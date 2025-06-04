<?php

namespace App\DataFixtures;

use App\Factory\PageFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
		PageFactory::createMany(200);

        $manager->flush();
    }
}
