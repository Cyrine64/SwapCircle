<?php

namespace App\Controller;

use App\Entity\Objet;
use App\Form\ObjetType;
use App\Repository\ObjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/objet')]
final class ObjetController extends AbstractController
{
    #[Route(name: 'app_objet_index', methods: ['GET'])]
    public function index(ObjetRepository $objetRepository): Response
    {
        return $this->render('objet/index.html.twig', [
            'objets' => $objetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_objet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $objet = new Objet();
        $objet->setDateAjout(new \DateTime());
        // Set default user if needed
        // $objet->setIdUtilisateur($this->getUser());
        
        $form = $this->createForm(ObjetType::class, $objet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($objet);
            $entityManager->flush();

            return $this->redirectToRoute('app_objet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('objet/new.html.twig', [
            'objet' => $objet,
            'form' => $form,
        ]);
    }

    #[Route('/{id_object}', name: 'app_objet_show', methods: ['GET'])]
    public function show(Objet $objet): Response
    {
        return $this->render('objet/show.html.twig', [
            'objet' => $objet,
        ]);
    }

    #[Route('/{id_object}/edit', name: 'app_objet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Objet $objet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ObjetType::class, $objet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_objet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('objet/edit.html.twig', [
            'objet' => $objet,
            'form' => $form,
        ]);
    }

    #[Route('/{id_object}', name: 'app_objet_delete', methods: ['POST'])]
    public function delete(Request $request, Objet $objet, EntityManagerInterface $entityManager): Response
    {
            $entityManager->remove($objet);
            $entityManager->flush();


        return $this->redirectToRoute('app_objet_index', [], Response::HTTP_SEE_OTHER);
    }
}
