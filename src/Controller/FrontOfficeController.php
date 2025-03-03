<?php

namespace App\Controller;

use App\Repository\RecyclageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TutorialRepository;

final class FrontOfficeController extends AbstractController
{
    #[Route('/', name: 'app_front_office')]
    public function index(): Response
    {
        return $this->render('front_office/index.html.twig', [
            'controller_name' => 'FrontOfficeController',
        ]);
    }

    #[Route('/services', name: 'front_office_services')]
    public function services(): Response
    {
        return $this->render('front_office/services.html.twig');
    }

    #[Route('/about', name: 'front_office_about')]
    public function about(): Response
    {
        return $this->render('front_office/about.html.twig');
    }

    #[Route('/blog', name: 'front_office_blog')]
    public function blog(): Response
    {
        return $this->render('front_office/blog.html.twig');
    }

    #[Route('/reclamation', name: 'front_office_reclamation')]
    public function reclamation(): Response
    {
        return $this->render('front_office/reclamation.html.twig');
    }

    #[Route('/contact', name: 'front_office_contact')]
    public function contact(): Response
    {
        return $this->render('recyclage/index.html.twig');
    }

    #[Route('/recyclages', name: 'front_office_recyclages')]
    public function recyclages(RecyclageRepository $recyclageRepository): Response
    {
        $recyclages = $recyclageRepository->findAll();

        return $this->render('recyclage/index.html.twig', [
            'recyclages' => $recyclages,
        ]);
    }

    #[Route('/tutorials', name: 'front_office_tutorials')]
    public function tutorials(TutorialRepository $tutorialRepository): Response
    {
        $tutorials = $tutorialRepository->findAll();

        return $this->render('tutorial/index.html.twig', [
            'tutorials' => $tutorials,
        ]);
    }



}
