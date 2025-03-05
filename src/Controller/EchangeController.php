<?php

namespace App\Controller;

use App\Entity\Echange;
use App\Entity\Objet;
use App\Entity\Utilisateur;
use App\Form\EchangeType;
use App\Repository\EchangeRepository;
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
        return $this->render('echange/index.html.twig', [
            'echanges' => $echangeRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_echange_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Objet $objet): Response
    {
        // Récupérer l'utilisateur (temporairement ID 1)
        $user = $entityManager->getRepository(Utilisateur::class)->find(1);
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('app_objet_index');
        }

        // Créer un nouvel échange
        $echange = new Echange();
        $echange->setNameEchange('Échange proposé pour ' . $objet->getNom());
        $echange->setDateEchange(new \DateTime());
        $echange->setUtilisateur($user);
        $echange->setObjet($objet);
        $echange->setImageEchange($objet->getImage() ?: 'default.jpg');
        $echange->setStatut('en_attente');
        $echange->setMessage(''); // Initialize with empty message
        
        // Créer le formulaire
        $form = $this->createForm(EchangeType::class, $echange, [
            'objet_propose' => $objet,
            'user' => $user,
        ]);
        
        // Traiter la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Ensure status is set before persisting
            if (!$echange->getStatut()) {
                $echange->setStatut('en_attente');
            }
            if (!$echange->getObjet()) {
                $echange->setObjet($objet);
            }
            
            $entityManager->persist($echange);
            $entityManager->flush();

            $this->addFlash('success', 'Votre proposition d\'échange a été envoyée avec succès !');
            return $this->redirectToRoute('app_echange_index');
        }

        // Afficher le formulaire
        return $this->render('echange/new.html.twig', [
            'form' => $form->createView(),
            'objet_propose' => $objet
        ]);
    }

    #[Route('/{id_echange}', name: 'app_echange_show', methods: ['GET'])]
    public function show(Echange $echange): Response
    {
        // Ajouter des messages flash en fonction du statut
        if ($echange->getStatut() === 'accepte') {
            $this->addFlash('success', 'Votre échange a été accepté ! Vous pouvez maintenant contacter le propriétaire pour organiser l\'échange.');
        } elseif ($echange->getStatut() === 'refuse') {
            $this->addFlash('danger', 'Désolé, votre proposition d\'échange a été refusée.');
        }

        return $this->render('echange/show.html.twig', [
            'echange' => $echange,
        ]);
    }

    #[Route('/{id_echange}/edit', name: 'app_echange_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Echange $echange, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EchangeType::class, $echange, [
            'objet_propose' => $echange->getObjet(),
            'user' => $echange->getUtilisateur(),
        ]);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_echange_index');
        }

        return $this->render('echange/edit.html.twig', [
            'echange' => $echange,
            'form' => $form->createView(),
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
    public function accept(Request $request, Echange $echange, EntityManagerInterface $entityManager): Response
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
            
            $this->addFlash('success', 'L\'échange a été accepté avec succès !');
        }
        
        return $this->redirectToRoute('app_echange_show', ['id_echange' => $echange->getIdEchange()]);
    }
    
    #[Route('/{id_echange}/refuse', name: 'app_echange_refuse', methods: ['POST'])]
    public function refuse(Request $request, Echange $echange, EntityManagerInterface $entityManager): Response
    {
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('refuse'.$echange->getIdEchange(), $request->request->get('_token'))) {
            // Mettre à jour le statut de l'échange
            $echange->setStatut('refuse');
            
            // L'objet reste disponible, pas besoin de modifier son état
            
            // Persister les changements
            $entityManager->flush();
            
            $this->addFlash('warning', 'L\'échange a été refusé.');
        }
        
        return $this->redirectToRoute('app_echange_show', ['id_echange' => $echange->getIdEchange()]);
    }
}
