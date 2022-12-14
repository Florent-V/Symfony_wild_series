<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Faker\Factory;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public static int $episodeIndex = 0;

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= SeasonFixtures::$seasonIndex; $i++) {
            
            $nbepisode = rand(12, 22);
            for ($j=1; $j <= $nbepisode; $j++) {
                $episode = new Episode();
                self::$episodeIndex++;
                $episode->setTitle($faker->sentence(4));
                $episode->setNumber($j);
                $episode->setSynopsis($faker->paragraph());
                $episode->setSeason($this->getReference('season_' . $i));
                $episode->setDuration($faker->numberBetween(40,60));
                $episode->setSlug($this->slugger->slug($episode->getTitle()));
                $manager->persist($episode);
            }  
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
