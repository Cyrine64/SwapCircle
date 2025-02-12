<?php

namespace App\Controller;

use App\Entity\Exchange;
use App\Entity\Objet;
use App\Repository\ObjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/exchange')]
class ExchangeController extends AbstractController
{
    #[Route('/propose/{id_object}', name: 'app_exchange_propose', methods: ['GET'])]
    public function propose(Objet $objet, ObjetRepository $objetRepository): Response
    {
        // Get the current user's objects for exchange
        $userObjects = $objetRepository->findAll();

        return $this->render('exchange/propose.html.twig', [
            'target_object' => $objet,
            'user_objects' => $userObjects,
        ]);
    }

    #[Route('/create/{target_id}/{offered_id}', name: 'app_exchange_create', methods: ['POST'])]
    public function create(
        int $target_id,
        int $offered_id,
        ObjetRepository $objetRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $targetObject = $objetRepository->find($target_id);
        $offeredObject = $objetRepository->find($offered_id);

        if (!$targetObject || !$offeredObject) {
            throw $this->createNotFoundException('Object not found');
        }

        $exchange = new Exchange();
        $exchange->setIdObjetDemande($targetObject);
        $exchange->setIdObjetOffert($offeredObject);
        $exchange->setIdDemandeur($this->getUser());
        $exchange->setIdOffreur($targetObject->getIdUtilisateur());
        $exchange->setDateEchange(new \DateTime());
        $exchange->setStatut('en_attente');

        $entityManager->persist($exchange);
        $entityManager->flush();

        $this->addFlash('success', 'Votre proposition d\'échange a été envoyée !');
        return $this->redirectToRoute('app_objet_show', ['id_object' => $target_id]);
    }
} 