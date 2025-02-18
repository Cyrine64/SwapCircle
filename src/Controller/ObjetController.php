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
        // Utiliser un utilisateur statique
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find(1);
        if (!$utilisateur) {
            throw new \Exception('L\'utilisateur statique avec ID 1 n\'existe pas');
        }
        
        $objet->setIdUtilisateur($utilisateur);
        $objet->setDateAjout(new \DateTime());
        $objet->setEtat('disponible');
        
        $form = $this->createForm(ObjetType::class, $objet, ['is_front' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Remplacer les caractères non alphanumériques par des tirets
                $safeFilename = preg_replace('/[^a-zA-Z0-9]/', '-', $originalFilename);
                $newFilename = strtolower($safeFilename).'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    $objet->setImage($newFilename); // Assurez-vous que le nom de l'image est enregistré
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image : ' . $e->getMessage());
                    return $this->redirectToRoute('app_objet_new');
                }
            }

            $entityManager->persist($objet);
            $entityManager->flush();

            $this->addFlash('success', 'L\'objet a été ajouté avec succès!');
            return $this->redirectToRoute('app_objet_index');
        }

        return $this->render('objet/new.html.twig', [
            'objet' => $objet,
            'form' => $form,
        ]);
    }

    #[Route('/front/new', name: 'app_front_objet_new', methods: ['GET', 'POST'])]
    public function newFront(Request $request, EntityManagerInterface $entityManager): Response
    {
        $objet = new Objet();
        // Utiliser un utilisateur statique
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find(1);
        if (!$utilisateur) {
            throw new \Exception('L\'utilisateur statique avec ID 1 n\'existe pas');
        }
        
        $objet->setIdUtilisateur($utilisateur);
        $objet->setDateAjout(new \DateTime());
        $objet->setEtat('disponible');
        
        $form = $this->createForm(ObjetType::class, $objet, ['is_front' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Remplacer les caractères non alphanumériques par des tirets
                $safeFilename = preg_replace('/[^a-zA-Z0-9]/', '-', $originalFilename);
                $newFilename = strtolower($safeFilename).'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    $objet->setImage($newFilename); // Assurez-vous que le nom de l'image est enregistré
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image : ' . $e->getMessage());
                    return $this->redirectToRoute('app_front_objet_new');
                }
            }

            $entityManager->persist($objet);
            $entityManager->flush();

            $this->addFlash('success', 'Votre objet a été ajouté avec succès!');
            return $this->redirectToRoute('front_office_index');
        }

        return $this->render('front_office/new_objet.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_objet_show', methods: ['GET'])]
    public function show(Objet $objet): Response
    {
        return $this->render('objet/show.html.twig', [
            'objet' => $objet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_objet_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'app_objet_delete', methods: ['POST'])]
    public function delete(Request $request, Objet $objet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$objet->getIdObjet(), $request->request->get('_token'))) {
            $entityManager->remove($objet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_objet_index', [], Response::HTTP_SEE_OTHER);
    }
}
