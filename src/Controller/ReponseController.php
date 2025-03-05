<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

#[Route('/reponse')]
final class ReponseController extends AbstractController
{
    private $reponseRepository;

    public function __construct(ReponseRepository $reponseRepository)
    {
        $this->reponseRepository = $reponseRepository;
    }

    #[Route(name: 'app_reponse_index', methods: ['GET'])]
    public function index(ReponseRepository $reponseRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        
        $limit = 5;
        
        $offset = ($page - 1) * $limit;
        
        $reponses = $reponseRepository->createQueryBuilder('r')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        
        $totalReponses = count($reponseRepository->findAll());
    
        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponses,
            'totalReponses' => $totalReponses,
            'currentPage' => $page,
            'totalPages' => ceil($totalReponses / $limit),
        ]);
    }  
    
    #[Route('/front', name: 'front_reponse_index', methods: ['GET'])]
    public function Frontindex(ReponseRepository $reponseRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        
        $limit = 5;
        
        $offset = ($page - 1) * $limit;
        
        $reponses = $reponseRepository->createQueryBuilder('r')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
        
        $totalReponses = count($reponseRepository->findAll());
    
        return $this->render('reponse/Frontindex.html.twig', [
            'reponses' => $reponses,
            'totalReponses' => $totalReponses,
            'currentPage' => $page,
            'totalPages' => ceil($totalReponses / $limit),
        ]);
    }  

    #[Route('/view/front/{id_reponse}', name: 'front_reponse_show', methods: ['GET'])]
    public function Frontshow(Reponse $reponse): Response
    {
        return $this->render('reponse/FrontView.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    #[Route('/back/search', name: 'app_reponse_search', methods: ['GET'])]
    public function search(Request $request, ReponseRepository $reponseRepository): JsonResponse
    {
        $searchQuery = $request->query->get('search', '');
        
        try {
            if (trim($searchQuery) === '') {
                $reponses = $reponseRepository->findAll();            
            } else {
                $reponses = $reponseRepository->searchReponse($searchQuery); 
            }
    
            $result = [];
            foreach ($reponses as $reponse) {
                $result[] = [
                    'id' => $reponse->getIdReponse(),
                    'titre' => $reponse->getReclamation()->getTitre(),
                    'contenu' => $reponse->getContenu(),
                    'dateReponse' => $reponse->getDateReponse(),
                ];
            }
    
            return new JsonResponse($result);  
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while fetching search results.'], 500);
        }
    } 

    #[Route('/new', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reponse = new Reponse();
        $reponse->setDateReponse(new \DateTime());

        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reponse);
            $entityManager->flush();

            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reponse/new.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/view/{id_reponse}', name: 'app_reponse_show', methods: ['GET'])]
    public function show(Reponse $reponse): Response
    {
        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    #[Route('/edit/{id_reponse}', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id_reponse}', name: 'app_reponse_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reponse->getIdReponse(), $request->getPayload()->getString('_token'))) {
            $reclamation = $reponse->getReclamation();
            if ($reclamation) {
                $reclamation->setReponse(null);
            }
    
            $entityManager->remove($reponse);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
    }
    

    #[Route('/stats', name: 'reponse_stats')]
    public function getReponseStatistics(ReponseRepository $responseRepository): Response
    {
        $responsesPerUser = $this->reponseRepository->countResponsesPerUser();
        $responsesPerReclamation = $this->reponseRepository->countResponsesPerReclamation();
        $averageLength = $this->reponseRepository->averageResponseLength();
        $responsesLastMonth = $this->reponseRepository->countResponsesLastMonth();

        return $this->render('reponse/stats.html.twig', [
            'responsesPerUser' => $responsesPerUser,
            'responsesPerReclamation' => $responsesPerReclamation,
            'averageLength' => $averageLength,
            'responsesLastMonth' => $responsesLastMonth,
        ]);
    }
    
}
