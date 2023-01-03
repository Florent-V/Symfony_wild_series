<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Form\SearchProgramType;
use App\Repository\CommentRepository;
use App\Repository\ProgramRepository;
use App\Service\ProgramDuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET', 'POST'])]
    public function index(
        ProgramRepository $programRepository,
        Request $request
    ): Response {

        $form = $this->createForm(SearchProgramType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->getData()['search'];
            $programs = $programRepository->findLikeName($search);
            dd($programs);
        } else {
            $programs = $programRepository->findAll();
        }

        return $this->renderForm(
            'program/index.html.twig',
            [
                'programs' => $programs,
                'form' => $form
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
            $program->setOwner($this->getUser());
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
        return $this->renderForm('program/new.html.twig', [
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

    #[Route('/{slug}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, ProgramRepository $programRepository): Response
    {
        if ($this->getUser() !== $program->getOwner()) {
            throw $this->createAccessDeniedException('Only the owner can edit the program!');
        }

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $programRepository->save($program, true);

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/watchlist', name: 'watchlist', methods: ['GET'])]
    public function addToWatchlist(
        Program $program,
        EntityManagerInterface $manager
    ): Response {

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with this id found in program\'s table.'
            );
        }
        /** @var \App\Entity\User $this */
        $user = $this->getUser();
        if ($user->isInWatchlist($program)) {
            $user->removeFromWatchlist($program);
        } else {
            $user->addToWatchlist($program);
        }
        $manager->flush();

        //return $this->redirectToRoute('program_show', ['slug' => $program->getSlug()], Response::HTTP_SEE_OTHER);
        return $this->json([
            'isInWatchlist' => $this->getUser()->isInWatchlist($program)
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
        methods: ['GET', 'POST']
    )]
    public function showEpisode(Request $request,
                                CommentRepository $commentRepository,
                                Program $program,
                                Season  $season,
                                Episode $episode): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $user = $this->getUser();

        $allCommentsEpisode = $commentRepository->findBy(
            ['episode' => $episode],
            ['id' => 'ASC']
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($user);
            $comment->setEpisode($episode);
            $commentRepository->save($comment, true);
            $this->addFlash('success', 'The comment has been posted !');
            return $this->redirectToRoute('program_episode_show', [
                'program' => $program->getId(),
                'season' => $season->getId(),
                'episode' => $episode->getId(),
            ]);
        }


        return $this->renderForm('program/episode_show.html.twig', [
            'season' => $season,
            'program' => $program,
            'episode' => $episode,
            'form' => $form,
            'user' => $user,
            'comment' => $comment,
            'allComments' => $allCommentsEpisode,
        ]);


    }

    #[Route('/{slug}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, ProgramRepository $programRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $program->getId(), $request->request->get('_token'))) {
            $programRepository->remove($program, true);
            // creation warning flash message
            $this->addFlash('danger', 'The program has been deleted');
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }
}
