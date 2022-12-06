<?php

namespace App\DataFixtures;
use Faker\Factory;
use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const TV_SHOWS = [
        [
            'title' => 'The Walking Dead',
            'synopsis' => "Des zombies envahissent la terre",
            'poster' => null,
        ],
        [
            'title' => 'The Crown',
            'synopsis' => "L'histoire de la famille royale anglaise",
            'poster' => null
        ],
        [
            'title' => 'Breaking Bad',
            'synopsis' => "Un enseignant de physique chimie décide de gagner un peu plus d'argent avec son talent",
            'poster' => null
        ],        
        [
            'title' => 'Sherlock Holmes',
            'synopsis' => "L'inspecteur de police anglais au Q.I très élevé !",
            'poster' => null
        ],        
        [
            'title' => 'Narco',
            'synopsis' => "Pablo Escobar, sa vie, son oeuvre",
            'poster' => null
        ],
        [
            'title' => 'Peaky Blinders',
            'synopsis' => "Une famille anglaise, les Shelby, étendent leur business dans le Birmingham d'après guerre",
            'poster' => null
        ],
        [
            'title' => 'The Boys',
            'synopsis' => "Des super-héros tournent mal et une équipe décide de passer à l'action et d'y mettre fin",
            'poster' => null
        ],
    ];

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $i = 1;
        foreach (self::TV_SHOWS as $tvShow) {
            $program = new Program();
            $program->setTitle($tvShow['title']);
            $program->setSynopsis($tvShow['synopsis']);
            $program->setCountry($faker->countryISOAlpha3());
            $program->setYear($faker->year());
            $program->setCategory($this->getReference('category_' . CategoryFixtures::CATEGORIES[rand(0, count(CategoryFixtures::CATEGORIES) - 1)]));
            $manager->persist($program);
            $this->addReference('program_' . $i, $program);
            $i++;
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            CategoryFixtures::class,
        ];
    }
}
