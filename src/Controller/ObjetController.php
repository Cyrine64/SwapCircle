<?php

namespace App\Controller;

use App\Entity\Objet;
use App\Entity\Utilisateur;
use App\Form\ObjetType;
use App\Repository\ObjetRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(Request $request, PaginatorInterface $paginator, ObjetRepository $objetRepository): Response
    {
        $queryBuilder = $objetRepository->createQueryBuilder('o');

        // Handle search with improved matching
        if ($search = $request->query->get('search')) {
            $searchTerms = array_filter(explode(' ', $search));
            $searchQuery = [];
            
            foreach ($searchTerms as $key => $term) {
                $searchQuery[] = '(LOWER(o.nom) LIKE LOWER(:search' . $key . ') OR LOWER(o.description) LIKE LOWER(:search' . $key . '))';
                $queryBuilder->setParameter('search' . $key, '%' . $term . '%');
            }
            
            if (!empty($searchQuery)) {
                $queryBuilder->andWhere(implode(' AND ', $searchQuery));
            }
        }

        // Handle category filter
        if ($category = $request->query->get('category')) {
            $queryBuilder->andWhere('o.categorie = :category')
                ->setParameter('category', $category);
        }

        // Handle date sorting
        $sortBy = $request->query->get('sortBy', 'desc');
        $queryBuilder->orderBy('o.date_ajout', strtoupper($sortBy));

        $page = max(1, $request->query->getInt('page', 1));
        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $page,
            4
        );

        if ($page > $pagination->getPageCount() && $pagination->getPageCount() > 0) {
            return $this->redirectToRoute('app_objet_index', ['page' => 1]);
        }

        return $this->render('objet/index.html.twig', [
            'pagination' => $pagination,
            'selectedCategory' => $category
        ]);
    }

    #[Route('/new', name: 'app_objet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
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

            // Send notification via Mercure
            $notificationSent = $notificationService->notify(
                'new_object', 
                'Un nouvel objet a été ajouté: ' . $objet->getNom(),
                [
                    'id' => $objet->getIdObjet(),
                    'name' => $objet->getNom(),
                    'image' => $objet->getImage(),
                    'category' => $objet->getCategorie() ?: 'Non catégorisé',
                ]
            );

            // Only log issue with notification, don't prevent the main functionality
            if (!$notificationSent) {
                // Log internal note about notification failure, but still show success to user
                // Code continues as normal
            }

            $this->addFlash('success', 'L\'objet a été ajouté avec succès!');
            return $this->redirectToRoute('app_objet_index');
        }

        return $this->render('objet/new.html.twig', [
            'objet' => $objet,
            'form' => $form,
        ]);
    }

    #[Route('/front/new', name: 'app_front_objet_new', methods: ['GET', 'POST'])]
    public function newFront(Request $request, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
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

            // Send notification via Mercure
            $notificationSent = $notificationService->notify(
                'new_object', 
                'Un nouvel objet a été ajouté: ' . $objet->getNom(),
                [
                    'id' => $objet->getIdObjet(),
                    'name' => $objet->getNom(),
                    'image' => $objet->getImage(),
                    'category' => $objet->getCategorie() ?: 'Non catégorisé',
                ]
            );

            // Only log issue with notification, don't prevent the main functionality
            if (!$notificationSent) {
                // Log internal note about notification failure, but still show success to user
                // Code continues as normal
            }

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
