<?php

namespace App\Controller;

use App\Entity\Echange;
use App\Entity\Objet;
use App\Entity\Utilisateur;
use App\Form\EchangeType;
use App\Repository\EchangeRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/echange')]
class EchangeController extends AbstractController
{
    #[Route('/', name: 'app_echange_index', methods: ['GET'])]
    public function index(EchangeRepository $echangeRepository): Response
    {
        return $this->render('back_office/echanges/index.html.twig', [
            'echanges' => $echangeRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_echange_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Objet $objet, NotificationService $notificationService): Response
    {
        // Récupérer l'utilisateur (temporairement ID 1)
        $user = $entityManager->getRepository(Utilisateur::class)->find(1);
        
        // Créer un nouvel échange
        $echange = new Echange();
        $echange->setUtilisateur($user);
        $echange->setObjet($objet);
        $echange->setNameEchange("Échange pour " . $objet->getNom());
        $echange->setDateEchange(new \DateTime());
        $echange->setStatut('en_attente');
        
        $form = $this->createForm(EchangeType::class, $echange);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($echange);
            $entityManager->flush();
            
            // Send notification via Mercure
            $notificationSent = $notificationService->notify(
                'new_exchange', 
                'Nouvelle proposition d\'échange pour: ' . $objet->getNom(),
                [
                    'id' => $echange->getIdEchange(),
                    'name' => $echange->getNameEchange(),
                    'object_id' => $objet->getIdObjet(),
                    'object_name' => $objet->getNom(),
                    'status' => 'en_attente'
                ]
            );
            
            // Log the notification status but don't prevent functionality
            if (!$notificationSent) {
                // Log that notification wasn't sent, but continue normally
            }

            $this->addFlash('success', 'Votre proposition d\'échange a été envoyée avec succès !');
            return $this->redirectToRoute('app_echange_index');
        }
        
        return $this->render('back_office/echanges/new.html.twig', [
            'form' => $form->createView(),
            'objet' => $objet,
        ]);
    }

    #[Route('/{id_echange}', name: 'app_echange_show', methods: ['GET'])]
    public function show(Echange $echange): Response
    {
        return $this->render('back_office/echanges/show.html.twig', [
            'echange' => $echange,
        ]);
    }

    #[Route('/{id_echange}/edit', name: 'app_echange_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Echange $echange, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EchangeType::class, $echange);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'échange a été modifié avec succès !');
            return $this->redirectToRoute('app_echange_index');
        }
        
        return $this->render('back_office/echanges/edit.html.twig', [
            'form' => $form->createView(),
            'echange' => $echange,
        ]);
    }

    #[Route('/{id_echange}/delete', name: 'app_echange_delete', methods: ['POST'])]
    public function delete(Request $request, Echange $echange, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$echange->getIdEchange(), $request->request->get('_token'))) {
            $entityManager->remove($echange);
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'échange a été supprimé avec succès !');
        }
        
        return $this->redirectToRoute('app_echange_index');
    }
    
    #[Route('/{id_echange}/accept', name: 'app_echange_accept', methods: ['POST'])]
    public function accept(Request $request, Echange $echange, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
    {
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('accept'.$echange->getIdEchange(), $request->request->get('_token'))) {
            // Mettre à jour le statut de l'échange
            $echange->setStatut('accepte');
            
            // Mettre à jour l'état de l'objet à "non disponible"
            $objet = $echange->getObjet();
            $objet->setEtat('attendu');
            
            // Persister les changements
            $entityManager->flush();
            
            // Send notification via Mercure
            $notificationSent = $notificationService->notify(
                'exchange_accepted', 
                'Proposition d\'échange acceptée pour: ' . $objet->getNom(),
                [
                    'id' => $echange->getIdEchange(),
                    'name' => $echange->getNameEchange(),
                    'object_id' => $objet->getIdObjet(),
                    'object_name' => $objet->getNom(),
                    'user_id' => $echange->getUtilisateur()->getIdUtilisateur()
                ]
            );
            
            // Log the notification status but don't prevent functionality
            if (!$notificationSent) {
                // Log that notification wasn't sent, but continue normally
            }

            $this->addFlash('success', 'L\'échange a été accepté avec succès !');
        }
        
        return $this->redirectToRoute('app_echange_show', ['id_echange' => $echange->getIdEchange()]);
    }
    
    #[Route('/{id_echange}/refuse', name: 'app_echange_refuse', methods: ['POST'])]
    public function refuse(Request $request, Echange $echange, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
    {
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('refuse'.$echange->getIdEchange(), $request->request->get('_token'))) {
            // Mettre à jour le statut de l'échange
            $echange->setStatut('refuse');
            
            // L'objet reste disponible, pas besoin de modifier son état
            
            // Persister les changements
            $entityManager->flush();
            
            // Send notification via Mercure
            $objet = $echange->getObjet();
            $notificationSent = $notificationService->notify(
                'exchange_refused', 
                'Proposition d\'échange refusée pour: ' . $objet->getNom(),
                [
                    'id' => $echange->getIdEchange(),
                    'name' => $echange->getNameEchange(),
                    'object_id' => $objet->getIdObjet(),
                    'object_name' => $objet->getNom(),
                    'user_id' => $echange->getUtilisateur()->getIdUtilisateur()
                ]
            );
            
            // Log the notification status but don't prevent functionality
            if (!$notificationSent) {
                // Log that notification wasn't sent, but continue normally
            }

            $this->addFlash('warning', 'L\'échange a été refusé.');
        }
        
        return $this->redirectToRoute('app_echange_show', ['id_echange' => $echange->getIdEchange()]);
    }
}
