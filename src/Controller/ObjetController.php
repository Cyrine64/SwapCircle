<?php

namespace App\Controller;

use App\Entity\Objet;
use App\Entity\Utilisateur;
use App\Form\ObjetType;
use App\Repository\ObjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Flex\Downloader;

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
    $utilisateur = $entityManager->getRepository(Utilisateur::class)->find(1);
    $objet->setIdUtilisateur($utilisateur);
    $objet->setDateAjout(new \DateTime());
    $objet->setEtat('disponible');
    $form = $this->createForm(ObjetType::class, $objet);
    $form->handleRequest($request); // Save filename in the database

    if ($form->isSubmitted() && $form->isValid()) {
        
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            // Generate a unique file name
            $newFilename = uniqid().'.'.$imageFile->guessExtension();

            // Move the file to the directory where images are stored
            $imageFile->move(
                $this->getParameter('images_directory'), // Define this parameter in services.yaml
                $newFilename
            );

            // Set the image path in the entity
            $objet->setImage($newFilename);
        }
        $entityManager->persist($objet);
        $entityManager->flush();
        

        return $this->redirectToRoute('app_objet_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('objet/new.html.twig', [
        'objet' => $objet,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id_objet}', name: 'app_objet_show', methods: ['GET'])]
    public function show(Objet $objet): Response
    {
        return $this->render('objet/show.html.twig', [
            'objet' => $objet,
        ]);
    }

    #[Route('/{id_objet}/edit', name: 'app_objet_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id_objet}', name: 'app_objet_delete', methods: ['POST'])]
    public function delete(Request $request, Objet $objet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$objet->getIdObjet(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($objet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_objet_index', [], Response::HTTP_SEE_OTHER);
    }
}
