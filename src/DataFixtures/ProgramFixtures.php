<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const TITLE = [
        'Welking Dead',
        'The Crown', 
        'Breaking Bad',  
        'Sherlock Holmes', 
        'Narco', 
        'Peaky Blinders',
        'The Boys',
    ];

    public const SYNOPSIS = [
        "Des zombies envahissent la terre",
        "L'histoire de la famille royale anglaise",
        "Un enseignant de physique chimie décide de gagner un peu plus d'argent avec son talent",
        "L'inspecteur de police anglais au Q.I très élevé !",
        "Pablo Escobar, sa vie, son oeuvre",
        "Une famille anglaise, les Shelby, étendent leur business dans le Birmingham d'après guerre",
        "Des super-héros tournent mal et une équipe décide de passer à l'action et d'y mettre fin",
    ];

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < count(self::TITLE); $i++) {
            $program = new Program();
            $program->setTitle(self::TITLE[$i]);
            $program->setSynopsis(self::SYNOPSIS[$i]);
            $program->setCategory($this->getReference('category_' . CategoryFixtures::CATEGORIES[rand(0, count(CategoryFixtures::CATEGORIES)-1)]));
            $manager->persist($program);

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