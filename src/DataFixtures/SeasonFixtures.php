<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Season;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public static int $seasonIndex = 0;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i=1; $i <= ProgramFixtures::$programIndex; $i++) {
            
            $nbseasons = rand(2, 10);
            $year = rand(1990, 2015);
            for ($j=1; $j <= $nbseasons; $j++) {
                $season = new Season();
                self::$seasonIndex++;
                $season->setNumber($j);
                $season->setYear($year + 2*$j);
                $season->setDescription($faker->paragraphs(3, true));
                $season->setProgram($this->getReference('program_' . $i));
                $manager->persist($season);
                $this->addReference('season_' . self::$seasonIndex, $season);
            }            
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
           ProgramFixtures::class,
        ];
    }
}
