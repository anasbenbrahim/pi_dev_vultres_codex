<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Rating;
use App\Entity\Publication;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RatingController extends AbstractController
{
    #[Route('/publication/rate/{id}', name: 'rate_publication', methods: ['PATCH'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function ratePublication(
        Request $request, 
        Publication $publication, 
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        $ratingValue = $data['rating'] ?? null;

        if (!is_numeric($ratingValue) || $ratingValue < 1 || $ratingValue > 5) {
            return new JsonResponse(['error' => 'Invalid rating value'], 400);
        }

        $ratingRepo = $entityManager->getRepository(Rating::class);
        $existingRating = $ratingRepo->findOneBy(['client' => $user, 'publication' => $publication]);

        if ($existingRating) {
            $existingRating->setRating($ratingValue);
        } else {
            $rating = new Rating();
            $rating->setClient($user);
            $rating->setPublication($publication);
            $rating->setRating($ratingValue);
            $entityManager->persist($rating);
        }

        $entityManager->flush();

        $averageRating = $this->calculateAverageRating($publication, $ratingRepo);

        return new JsonResponse(['success' => true, 'averageRating' => $averageRating]);
    }

    private function calculateAverageRating(Publication $publication, $ratingRepo): float
    {
        $ratings = $ratingRepo->findBy(['publication' => $publication]);
        if (empty($ratings)) {
            return 0;
        }
        $total = array_reduce($ratings, fn($sum, $rating) => $sum + $rating->getRating(), 0);
        return round($total / count($ratings), 1);
    }

    public function getTopRatedPublications(EntityManagerInterface $entityManager): array
    {
        $publications = $entityManager->getRepository(Publication::class)->findAll();
        $topRated = [];
        
        foreach ($publications as $publication) {
            $ratings = $entityManager->getRepository(Rating::class)->findBy(['publication' => $publication]);
            $average = $this->calculateAverageRating($publication, $entityManager->getRepository(Rating::class));
            if (count($ratings) >= 2 && $average > 3) {
                $topRated[] = $publication;
            }
        }
        return $topRated;
    }
}
