<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Service\ReclamationAnalyzer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    private $httpClient;
    private $reclamationAnalyzer;

    public function __construct(HttpClientInterface $httpClient, ReclamationAnalyzer $reclamationAnalyzer)
    {
        $this->httpClient = $httpClient;
        $this->reclamationAnalyzer = $reclamationAnalyzer;
    }

    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $reclamationRepository->createQueryBuilder('r')
            ->leftJoin('r.reponses', 'rep')
            ->addSelect('rep')
            ->orderBy('r.date_reclamation', 'DESC')
            ->getQuery();

        $reclamations = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $reclamation->setDateReclamation(new \DateTime());

        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réclamation a été soumise avec succès!');

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/new/front', name: 'app_reclamation_new_front', methods: ['GET', 'POST'])]
    public function newFront(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $reclamation->setDateReclamation(new \DateTime());
        $reclamation->setStatut('En attente');
        
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Analyse de la réclamation
            $analysis = $this->reclamationAnalyzer->analyzeReclamation($reclamation->getMessage());
            
            // Mise à jour de la réclamation avec l'analyse
            $reclamation->setPriorite($analysis['priority']);
            $reclamation->setCategorie($analysis['category']);
            
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réclamation a été soumise avec succès!');
            $this->addFlash('info', $analysis['suggested_response']);

            $weather = $this->getWeather();
            return $this->render('reclamation/success.html.twig', [
                'weather' => $weather,
                'analysis' => $analysis
            ]);
        }

        return $this->render('reclamation/newFront.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('info', 'La réclamation a été mise à jour avec succès!');

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/status/{status}', name: 'app_reclamation_status', methods: ['POST'])]
    public function updateStatus(Reclamation $reclamation, string $status, EntityManagerInterface $entityManager): Response
    {
        $reclamation->setStatut($status);
        $entityManager->flush();

        if ($status === 'En cours') {
            $this->addFlash('info', 'La réclamation est en cours de traitement');
        } elseif ($status === 'Résolu') {
            $this->addFlash('success', 'La réclamation a été résolue avec succès!');
        }

        return $this->redirectToRoute('app_reclamation_index');
    }

    #[Route('/{id}/repondre', name: 'app_reclamation_repondre', methods: ['POST'])]
    public function repondre(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $contenu = $request->request->get('reponse');
        
        if (!$contenu) {
            $this->addFlash('error', 'La réponse ne peut pas être vide');
            return $this->redirectToRoute('app_reclamation_show', ['id' => $reclamation->getId()]);
        }

        $reponse = new Reponse();
        $reponse->setContenu($contenu);
        $reponse->setDateReponse(new \DateTime());
        $reponse->setReclamation($reclamation);
        
        // Mise à jour du statut de la réclamation
        $reclamation->setStatut('En cours');
        
        $entityManager->persist($reponse);
        $entityManager->flush();

        $this->addFlash('success', 'Votre réponse a été enregistrée');
        return $this->redirectToRoute('app_reclamation_show', ['id' => $reclamation->getId()]);
    }

    private function getWeather(): array
    {
        try {
            $response = $this->httpClient->request('GET', 'http://api.weatherapi.com/v1/current.json', [
                'query' => [
                    'key' => '8b0e4a2e5fmsh4395c3e4900eb21p1fd5f7jsn9a429a42f68c',
                    'q' => 'Tunis',
                    'aqi' => 'no'
                ]
            ]);

            $data = $response->toArray();
            return [
                'temperature' => $data['current']['temp_c'],
                'condition' => $data['current']['condition']['text'],
                'icon' => $data['current']['condition']['icon']
            ];
        } catch (\Exception $e) {
            return [
                'temperature' => null,
                'condition' => 'Non disponible',
                'icon' => null
            ];
        }
    }
}
