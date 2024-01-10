<?php

namespace App\Controller;

use App\Repository\ActorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/search', name: 'app_search_')]
class SearchController extends AbstractController
{
    #[Route('/actor', name: 'actor', methods: ['GET'])]
    public function actor(Request $request, ActorRepository $actorRepository): JsonResponse
    {
        $term = $request->query->get('term');

        if (!$term) {
            return new JsonResponse([]);
        }

        $actors = $actorRepository->searchActors($term);

        $data = [];
        foreach ($actors as $actor) {
            $data[] = [
            'id' => $actor->getId(),
            'name' => $actor->getLastname() . ' ' . $actor->getFirstname(),
            ];
        }
        return new JsonResponse($data);
    }
}