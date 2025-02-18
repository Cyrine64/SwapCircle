<?php

namespace App\Controller;

use App\Entity\Blog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/article')]
final class ArticleController extends AbstractController
{
    #[Route('', name: 'app_blog_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer les blogs
        $blogs = $entityManager->getRepository(Blog::class)->findAll();
        
        // Retourner la vue avec la variable blogs
        return $this->render('front_office/article.html.twig', [
            'blogs' => $blogs,  // Assure-toi que la variable blogs est bien envoyée
        ]);
    }
}