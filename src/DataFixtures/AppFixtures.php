<?php

namespace App\DataFixtures;

use App\Factory\PageFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
		foreach(['gallery','main','press','services'] as $item){
			PageFactory::createMany(
				50,
				fn(int $i) => ['uri' => "$item/$i"]);
		}

        $manager->flush();
    }
}
