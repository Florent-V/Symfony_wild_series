<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Faker\Factory;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 500; $i++) {
            $episode = new Episode();
            $episode->setTitle($faker->sentence(4));
            $episode->setNumber($faker->numberBetween(0,15));
            $episode->setSynopsis($faker->paragraph());
            $episode->setSeason($this->getReference('season_' . $faker->numberBetween(1,50)));
            $manager->persist($episode);
    }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
           SeasonFixtures::class,
        ];
    }
}
