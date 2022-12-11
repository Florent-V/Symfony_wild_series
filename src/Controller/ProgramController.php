<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\SeasonRepository;
use App\Repository\ProgramRepository;
use App\Service\ProgramDuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        return $this->render(
            'program/index.html.twig',
            [
                'programs' => $programs,
            ]
        );
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request           $request,
                        MailerInterface   $mailer,
                        ProgramRepository $programRepository,
                        SluggerInterface  $slugger): Response
    {
        $program = new Program();

        // Create the form, linked with $category
        $form = $this->createForm(ProgramType::class, $program);

        // Get data from HTTP request
        $form->handleRequest($request);

        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Deal with the submitted data
            // For example : persiste & flush the entity
            // And redirect to a route that display the result
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $programRepository->save($program, true);

            //Sending Email
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('physchim115@gmail.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));

            $mailer->send($email);


            // Once the form is submitted, valid and the data inserted in database, creation success flash message
            $this->addFlash('success', 'The new program has been created');

            //Redirect to categories list
            return $this->redirectToRoute('program_index');
        }


        // Render the form (best practice)
        return $this->renderForm('category/new.html.twig', [
            'program' => $program,
            'form' => $form
        ]);
    }

    #[Route('/{slug}', name: 'show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Program $program, ProgramDuration $programDuration): Response
    {

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with this id found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'programDuration' => $programDuration->calculate($program),
        ]);
    }

    #[Route(
        '/{program}/seasons/{season}',
        name: 'season_show',
        requirements: [
            'program' => '\d+',
            'season' => '\d+'
        ],
        methods: ['GET']
    )]
    public function showSeason(Program $program, Season $season): Response
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
        name: 'episode_show',
        requirements: [
            'program' => '\d+',
            'season' => '\d+',
            'episode' => '\d+',
        ],
        methods: ['GET']
    )]
    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {

        return $this->render('program/episode_show.html.twig', [
            'season' => $season,
            'program' => $program,
            'episode' => $episode,
        ]);


    }
}
