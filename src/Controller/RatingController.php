<?php

namespace App\Controller;

use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RatingController extends AbstractController
{
    #[Route('/publication/rate/{id}', name: 'publication_rate', methods: ['POST'])]
    public function rate(Publication $publication, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $rating = $data['rating'] ?? null;

        if ($rating !== null && $rating >= 1 && $rating <= 5) {
            $publication->setRating($rating);
            $entityManager->flush();

            return new JsonResponse(['success' => true, 'rating' => $rating]);
        }

        return new JsonResponse(['success' => false], 400);
    }
}
