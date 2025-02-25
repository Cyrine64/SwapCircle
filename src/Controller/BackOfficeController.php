<?php

namespace App\Controller;

use App\Entity\Objet;
use App\Entity\Echange;
use App\Entity\Utilisateur;
use App\Form\ObjetType;
use App\Form\EchangeType;
use App\Repository\ObjetRepository;
use App\Repository\EchangeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboard')]
final class BackOfficeController extends AbstractController
{
    #[Route('', name: 'app_back_office')]
    public function index(): Response
    {
        return $this->render('back_office/index.html.twig', [
            'controller_name' => 'BackOfficeController',
        ]);
    }

    // Routes pour les objets
    #[Route('/objets', name: 'app_back_office_objets')]
    public function listObjets(ObjetRepository $objetRepository): Response
    {
        return $this->render('back_office/objet/index.html.twig', [
            'objets' => $objetRepository->findAll(),
        ]);
    }

    #[Route('/objets/metier/{metier}', name: 'app_back_office_objets_by_metier')]
    public function listObjetsByMetier(ObjetRepository $objetRepository, string $metier): Response
    {
        return $this->render('back_office/objet/index.html.twig', [
            'objets' => $objetRepository->findByMetier($metier),
            'current_metier' => $metier
        ]);
    }

    #[Route('/objets/statistiques', name: 'app_back_office_objets_stats')]
    public function objetStatistiques(ObjetRepository $objetRepository): Response
    {
        return $this->render('back_office/objet/statistiques.html.twig', [
            'statistiques' => $objetRepository->getStatistiquesByMetier()
        ]);
    }

    #[Route('/objets/recherche-avancee', name: 'app_back_office_objets_recherche_avancee')]
    public function rechercheAvancee(Request $request, ObjetRepository $objetRepository): Response
    {
        $metier = $request->query->get('metier');
        $categorie = $request->query->get('categorie');
        
        $objets = [];
        if ($metier !== null || $categorie !== null) {
            $objets = $objetRepository->findByMetierAndCategorie($metier, $categorie);
        } else {
            $objets = $objetRepository->findAll();
        }
        
        return $this->render('back_office/objet/recherche_avancee.html.twig', [
            'objets' => $objets,
            'metier' => $metier,
            'categorie' => $categorie
        ]);
    }

    #[Route('/objet/new', name: 'app_back_office_objet_new')]
    public function newObjet(Request $request, EntityManagerInterface $entityManager): Response
    {
        $objet = new Objet();
        $form = $this->createForm(ObjetType::class, $objet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $objet->setDateAjout(new \DateTime());
            $objet->setIdUtilisateur($entityManager->getRepository(Utilisateur::class)->findOneBy(['id_utilisateur' => 1]));
            $entityManager->persist($objet);
            $entityManager->flush();

            return $this->redirectToRoute('app_back_office_objets');
        }

        return $this->render('back_office/objet/edit.html.twig', [
            'objet' => $objet,
            'form' => $form,
            'button_label' => 'Créer'
        ]);
    }

    #[Route('/objet/{id}', name: 'app_back_office_objet_show')]
    public function showObjet(Objet $objet): Response
    {
        return $this->render('back_office/objet/show.html.twig', [
            'objet' => $objet,
        ]);
    }

    #[Route('/objet/{id}/edit', name: 'app_back_office_objet_edit')]
    public function editObjet(Request $request, Objet $objet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ObjetType::class, $objet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_back_office_objets');
        }

        return $this->render('back_office/objet/edit.html.twig', [
            'objet' => $objet,
            'form' => $form,
            'button_label' => 'Modifier'
        ]);
    }

    #[Route('/objet/{id}/delete', name: 'app_back_office_objet_delete', methods: ['POST'])]
    public function deleteObjet(Request $request, Objet $objet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$objet->getIdObjet(), $request->request->get('_token'))) {
            $entityManager->remove($objet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_back_office_objets');
    }

    // Routes pour les échanges
    #[Route('/echanges', name: 'app_back_office_echanges')]
    public function listEchanges(EchangeRepository $echangeRepository): Response
    {
        return $this->render('back_office/echanges/index.html.twig', [
            'echanges' => $echangeRepository->findAll(),
        ]);
    }

    #[Route('/echange/new', name: 'app_back_office_echange_new')]
    public function newEchange(Request $request, EntityManagerInterface $entityManager): Response
    {
        $echange = new Echange();
        $echange->setDateEchange(new \DateTime());
        $echange->setMessage('');
        $echange->setNameEchange('');
        $echange->setImageEchange('');
        
        // Créer un utilisateur et un objet fictifs pour le back-office
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->findOneBy([]);
        $objet = $entityManager->getRepository(Objet::class)->findOneBy([]);
        
        if (!$utilisateur || !$objet) {
            $this->addFlash('error', 'Vous devez d\'abord créer au moins un utilisateur et un objet.');
            return $this->redirectToRoute('app_back_office_echanges');
        }
        
        $echange->setUtilisateur($utilisateur);
        $echange->setObjet($objet);
        
        $form = $this->createForm(EchangeType::class, $echange, [
            'objet_propose' => $objet,
            'user' => $utilisateur
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($echange);
            $entityManager->flush();

            return $this->redirectToRoute('app_back_office_echanges');
        }

        return $this->render('back_office/echanges/edit.html.twig', [
            'echange' => $echange,
            'form' => $form->createView(),
            'button_label' => 'Créer'
        ]);
    }

    #[Route('/echange/{id}', name: 'app_back_office_echange_show')]
    public function showEchange(Echange $echange): Response
    {
        return $this->render('back_office/echanges/show.html.twig', [
            'echange' => $echange,
        ]);
    }

    #[Route('/echange/{id}/edit', name: 'app_back_office_echange_edit')]
    public function editEchange(Request $request, Echange $echange, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EchangeType::class, $echange, [
            'objet_propose' => $echange->getObjet(),
            'user' => $echange->getUtilisateur(),
        ]);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_back_office_echanges');
        }

        return $this->render('back_office/echanges/edit.html.twig', [
            'echange' => $echange,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/echange/{id}/delete', name: 'app_back_office_echange_delete', methods: ['POST'])]
    public function deleteEchange(Request $request, Echange $echange, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$echange->getIdEchange(), $request->request->get('_token'))) {
            $entityManager->remove($echange);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_back_office_echanges');
    }

    #[Route('/echange/{id}/accept', name: 'app_back_office_echange_accept', methods: ['POST'])]
    public function acceptEchange(Request $request, Echange $echange, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('accept'.$echange->getIdEchange(), $request->request->get('_token'))) {
            $echange->setStatut('accepte');
            $entityManager->flush();
            
            // Mettre à jour l'état de l'objet à "échangé"
            $objet = $echange->getObjet();
            if ($objet) {
                $objet->setEtat('echange');
                $entityManager->flush();
            }

            $this->addFlash('success', 'L\'échange a été accepté avec succès.');
        }

        return $this->redirectToRoute('app_back_office_echange_show', ['id' => $echange->getIdEchange()]);
    }

    #[Route('/echange/{id}/refuse', name: 'app_back_office_echange_refuse', methods: ['POST'])]
    public function refuseEchange(Request $request, Echange $echange, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('refuse'.$echange->getIdEchange(), $request->request->get('_token'))) {
            $echange->setStatut('refuse');
            $entityManager->flush();

            $this->addFlash('success', 'L\'échange a été refusé.');
        }

        return $this->redirectToRoute('app_back_office_echange_show', ['id' => $echange->getIdEchange()]);
    }
}
