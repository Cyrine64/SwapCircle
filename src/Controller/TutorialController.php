<?php

namespace App\Controller;
use App\Form\TutorialType;

use App\Entity\Tutorial;
use App\Entity\Recyclage;
use App\Entity\Utilisateur;
use App\Repository\TutorialRepository;
use App\Repository\RecyclageRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/tutorial')]
class TutorialController extends AbstractController
{

    /*
    #[Route('/', name: 'tutorial_index', methods: ['GET'])]
    public function index(TutorialRepository $tutorialRepository): Response
    {
        return $this->render('tutorial/index.html.twig', [
            'tutorials' => $tutorialRepository->findAll(),
        ]);
    }*/

    

    #[Route('/', name: 'tutorial_index', methods: ['GET'])]
public function index(TutorialRepository $tutorialRepository, Request $request): Response
{
    $search = $request->query->get('search', '');

    if ($search) {
        // Si une recherche est effectuée, filtrer les tutoriels
        $tutorials = $tutorialRepository->findByDescription($search);
    } else {
        // Sinon, afficher tous les tutoriels
        $tutorials = $tutorialRepository->findAll();
    }

    return $this->render('tutorial/index.html.twig', [
        'tutorials' => $tutorials,
    ]);
}
/*

#[Route('/', name: 'tutorial_index', methods: ['GET'])]
public function index(TutorialRepository $tutorialRepository, Request $request, PaginatorInterface $paginator): Response
{
    $search = $request->query->get('search', '');

    // Création d'un QueryBuilder pour la pagination
    $queryBuilder = $tutorialRepository->createQueryBuilder('t');

    if ($search) {
        $queryBuilder->where('t.description LIKE :search')
                     ->setParameter('search', '%' . $search . '%');
    }

    // Utilisation correcte de KnpPaginator
    $pagination = $paginator->paginate(
        $queryBuilder,                         // QueryBuilder et non un tableau
        $request->query->getInt('page', 1),    // Page actuelle
        3                                      // Limite d'éléments par page
    );

    return $this->render('tutorial/index.html.twig', [
        'tutorials' => $pagination, // Ceci est un objet SlidingPaginationInterface
    ]);
}*/

    #[Route('/tutorial/new', name: 'tutorial_new')]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $tutorial = new Tutorial();
    $form = $this->createForm(TutorialType::class, $tutorial);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($tutorial);
        $entityManager->flush();

        $this->addFlash('success', 'Tutoriel ajouté avec succès !');

        return $this->redirectToRoute('tutorial_index');
    }

    return $this->render('tutorial/new.html.twig', [
        'form' => $form->createView(), // ✅ Envoie bien la variable 'form'
    ]);
}


/*
    #[Route('/new', name: 'tutorial_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $em, 
        RecyclageRepository $recyclageRepo, 
        UtilisateurRepository $utilisateurRepo
    ): Response {
        if ($request->isMethod('POST')) {
            $tutorial = new Tutorial();
            $description = $request->request->get('description');
            $vidURL = $request->request->get('vid_URL');
            $recyclageId = $request->request->get('recyclage');
            $utilisateurId = $request->request->get('utilisateur');

            $recyclage = $recyclageRepo->find($recyclageId);
            $utilisateur = $utilisateurRepo->find($utilisateurId);

            if (!$recyclage || !$utilisateur) {
                $this->addFlash('error', 'Recyclage ou Utilisateur invalide.');
                return $this->redirectToRoute('tutorial_new');
            }

            $tutorial->setDescription($description);
            $tutorial->setVidURL($vidURL);
            $tutorial->setDateCreation(new \DateTime());
            $tutorial->setRecyclage($recyclage);
            $tutorial->setUtilisateur($utilisateur);

            $em->persist($tutorial);
            $em->flush();

            $this->addFlash('success', 'Tutoriel ajouté avec succès !');
            return $this->redirectToRoute('tutorial_index');
        }

        return $this->render('tutorial/new.html.twig', [
            'recyclages' => $recyclageRepo->findAll(),
            'utilisateurs' => $utilisateurRepo->findAll(),
        ]);
    }*/

    #[Route('/{id}', name: 'tutorial_show', methods: ['GET'])]
    public function show(Tutorial $tutorial): Response
    {
        return $this->render('tutorial/show.html.twig', [
            'tutorial' => $tutorial,
        ]);
    }

    #[Route('/{id}/delete', name: 'tutorial_delete', methods: ['POST'])]
    public function delete(Request $request, Tutorial $tutorial, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tutorial->getIdTutorial(), $request->request->get('_token'))) {
            $em->remove($tutorial);
            $em->flush();
            $this->addFlash('success', 'Tutoriel supprimé avec succès.');
        }

        return $this->redirectToRoute('tutorial_index');
    }
    #[Route('/tutorial/{id}/edit', name: 'tutorial_edit')]
    public function edit(Request $request, Tutorial $tutorial, EntityManagerInterface $em): Response
    {
        // Crée le formulaire basé sur l'entité Tutorial
        $form = $this->createForm(TutorialType::class, $tutorial);
        $form->handleRequest($request);

        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            // Message de succès et redirection
            $this->addFlash('success', 'Tutoriel mis à jour avec succès!');
            return $this->redirectToRoute('tutorial_index');
        }

        // Rendu du formulaire d'édition
        return $this->render('tutorial/edit.html.twig', [
            'form' => $form->createView(),
            'tutorial' => $tutorial
        ]);
    }
/*
    #[Route('/tutorial/new', name: 'tutorial_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $tutorial = new Tutorial();

        $form = $this->createForm(TutorialType::class, $tutorial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tutorial);
            $em->flush();

            $this->addFlash('success', 'Tutoriel ajouté avec succès!');

            return $this->redirectToRoute('tutorial_index');
        }

        return $this->render('tutorial/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }*/
}
