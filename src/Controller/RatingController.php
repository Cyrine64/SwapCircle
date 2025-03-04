<?php

namespace App\Controller;

use App\Entity\Blog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AbstractController
{
    #[Route('/blog/{id}/rate', name: 'blog_rate', methods: ['POST'])]
    public function rateBlog(Blog $blog, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $rating = $request->request->get('rating');
        
        if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
            return new JsonResponse(['error' => 'Invalid rating value'], 400);
        }

        $blog->addRating((float) $rating);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'newRating' => $blog->getAverageRating(),
            'ratingCount' => $blog->getRatingCount()
        ]);
    }
}
