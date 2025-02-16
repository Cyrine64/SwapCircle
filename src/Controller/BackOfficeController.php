<?php
namespace App\Controller;

use App\Repository\ObjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BackOfficeController extends AbstractController
{
    #[Route('/dashboard', name: 'app_back_office')]
    public function index(): Response
    {
        return $this->render('back_office/index.html.twig', [
            'controller_name' => 'BackOfficeController',
        ]);
    }

    #[Route('/dashboard/recyclage', name: 'app_back_office_recyclage')]
    public function recyclageIndex(ObjetRepository $objetRepository): Response
    {
        $objets = $objetRepository->findAll();
    
        return $this->render('back_office/recyclage/index.html.twig', [
            'objets' => $objets,
        ]);
    }
}

