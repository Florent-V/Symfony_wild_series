<?php

namespace App\DataFixtures;
use Faker\Factory;
use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public static int $programIndex = 0;

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
        [
            'title' => 'Wednesday',
            'synopsis' => "A présent étudiante à la singulière Nevermore Academy, Wednesday Addams tente de s'adapter auprès des autres élèves tout en enquêtant à la suite d'une série de meurtres qui terrorise la ville...",
            'poster' => null
        ],
        [
            'title' => 'Dahmer',
            'synopsis' => "Sur plus d'une décennie, Jeffrey Dahmer a massacré 17 adolescents et jeunes hommes avant son inculpation. Comment a-t-il pu échapper aux forces de l'ordre pendant si longtemps ?",
            'poster' => null
        ],
        [
            'title' => 'La casa de papel',
            'synopsis' => "El Profesor est le cerveau d'un groupe de huit criminels dont l'ambition est de réaliser le braquage parfait : pourquoi attaquer une bijouterie ou une banque, quand on peut s’infiltrer dans l’antre des antres, l’usine de la Monnaie et des Timbres, et fabriquer son propre argent. ",
            'poster' => null
        ],
        [
            'title' => 'Stranger Things',
            'synopsis' => "Un soir de novembre 1983 dans la ville américaine d'Hawkins en Indiana, le jeune Will Byers âgé de douze ans disparaît brusquement sans laisser de traces, hormis son vélo. Plusieurs personnages vont alors tenter de le retrouver.",
            'poster' => null
        ],
    ];

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        foreach (self::TV_SHOWS as $tvShow) {
            self::$programIndex++;
            $program = new Program();
            $program->setTitle($tvShow['title']);
            $program->setSynopsis($tvShow['synopsis']);
            $program->setCountry($faker->countryISOAlpha3());
            $program->setYear($faker->year());
            $program->setCategory($this->getReference('category_' . CategoryFixtures::CATEGORIES[rand(0, count(CategoryFixtures::CATEGORIES) - 1)]));
            $program->setSlug($this->slugger->slug($program->getTitle()));
            $manager->persist($program);
            $this->addReference('program_' . self::$programIndex, $program);
            
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            CategoryFixtures::class,
        ];
    }
}
