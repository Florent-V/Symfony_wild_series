<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\SeasonRepository;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ProgramRepository $programRepository) //: Response
    {
        $programs = $programRepository->findAll();

        return $this->render(
            'program/index.html.twig',
            [
                'programs' => $programs,
            ]
        );
    }

    #[Route('/new', methods: ['GET', 'POST'], name: 'new')]
    public function new(Request $request, ProgramRepository $programRepository): Response
    {
        $program = new Program();

        // Create the form, linked with $category
        $form = $this->createForm(ProgramType::class, $program);

        // Get data from HTTP request
        $form->handleRequest($request);

        // Was the form submitted ?
        if ($form->isSubmitted()) {
            // Deal with the submitted data
            // For example : persiste & flush the entity
            // And redirect to a route that display the result
            $programRepository->save($program, true);
            //Redirect to categories list
            return $this->redirectToRoute('program_index');
    
    
            }


        // Render the form (best practice)
        return $this->renderForm('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'], name: 'show')]
    public function show(Program $program): Response
    {
       
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with this id found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }

    #[Route(
        '/{program}/seasons/{season}',
        requirements: [
            'program' => '\d+',
            'season' => '\d+'
        ],
        methods: ['GET'],
        name: 'season_show'
    )]
    public function showSeason(Program $program, Season $season)
    {
        // $program = $programRepository->findOneBy(
        //     ['id' => $programId],
        // );

        // if (!$program) {
        //     throw $this->createNotFoundException(
        //         'No program with this id : found in program\'s table.'
        //     );
        // }

        // $season = $seasonRepository->findOneBy(
        //     [
        //         'program' => $program,
        //         'id' => $seasonId,
        //     ]
        // );

        // if (!$season) {
        //     throw $this->createNotFoundException(
        //         'No season with this id : found in program\'s table.'
        //     );
        // }

        return $this->render('program/season_show.html.twig', [
            'season' => $season,
            'program' => $program,
        ]);


    }

    #[Route(
        '/{program}/seasons/{season}/episode/{episode}',
        requirements: [
            'program' => '\d+',
            'season' => '\d+',
            'episode' => '\d+',
        ],
        methods: ['GET'],
        name: 'episode_show'
    )]
    public function showEpisode(Program $program, Season $season, Episode $episode)
    {

        return $this->render('program/episode_show.html.twig', [
            'season' => $season,
            'program' => $program,
            'episode' => $episode,
        ]);


    }
}
