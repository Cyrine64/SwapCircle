<?php

namespace App\Controller;

use App\Entity\Recyclage;
use App\Repository\RecyclageRepository;
use App\Repository\ObjetRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Repository\UtilisateurRepository;

class RecyclageController extends AbstractController
{

    
    #[Route('/recyclages', name: 'recyclage_index')]
    public function index(RecyclageRepository $recyclageRepository): Response
    {
        $recyclages = $recyclageRepository->findAll();

        return $this->render('recyclage/index.html.twig', [
            'recyclages' => $recyclages,
        ]);
    }


/*
#[Route('/recyclage', name: 'recyclage_index', methods: ['GET'])]
public function index(RecyclageRepository $recyclageRepository): Response
{
    // Récupère les types de recyclage distincts depuis la base de données
    $typesRecyclage = $recyclageRepository->createQueryBuilder('r')
        ->select('DISTINCT r.type_recyclage')
        ->getQuery()
        ->getResult();

    // Transforme en tableau simple si nécessaire
    $typesRecyclage = array_map(fn($item) => $item['type_recyclage'], $typesRecyclage);

    return $this->render('recyclage/index.html.twig', [
        'recyclages' => $recyclageRepository->findAll(),
        'typesRecyclages' => $typesRecyclage, // Passage de la variable au template
    ]);
}

*/




       /*    #[Route('/recyclages/{id}', name: 'recyclage_show', methods: ['GET'])]
    public function show(RecyclageRepository $recyclageRepository, int $id): Response
    {
        $recyclage = $recyclageRepository->find($id);
    
        if (!$recyclage) {
            throw $this->createNotFoundException('Recyclage non trouvé.');
        }
    
        return $this->render('recyclage/show.html.twig', [
            'recyclage' => $recyclage,
        ]);
    }


    */
    // Route pour afficher les détails d'un recyclage


    #[Route('/recyclages/{id<\d+>}', name: 'recyclage_show', methods: ['GET'])]
    public function show(RecyclageRepository $recyclageRepository, int $id): Response
    {
        $recyclage = $recyclageRepository->find($id);
    
        if (!$recyclage) {
            throw $this->createNotFoundException('Recyclage non trouvé.');
        }
    
        return $this->render('recyclage/show.html.twig', [
            'recyclage' => $recyclage,
        ]);
    }
    
    


    /*#[Route('/recyclages/{id}', name: 'recyclage_show', methods: ['GET'])]
    public function show(Recyclage $recyclage): Response
    {
        return $this->render('recyclage/show.html.twig', [
            'recyclage' => $recyclage,
        ]);
    }*/


 

    // Route pour éditer un recyclage
    /*
    #[Route('/recyclages/{id}/edit', name: 'recyclage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recyclage $recyclage, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $recyclage->setObjet($request->request->get('objet'));
            $recyclage->setTypeRecyclage($request->request->get('typeRecyclage'));
            $recyclage->setCommentaire($request->request->get('commentaire'));
            $entityManager->flush();

            return $this->redirectToRoute('recyclage_index');
        }

        return $this->render('recyclage/edit.html.twig', [
            'recyclage' => $recyclage,
        ]);
    }*/
    // Injection de ObjetRepository
/*
#[Route('/recyclages/{id}/edit', name: 'recyclage_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Recyclage $recyclage, EntityManagerInterface $entityManager, ObjetRepository $objetRepository): Response
{
    if ($request->isMethod('POST')) {
        // Récupérer l'ID de l'objet depuis le formulaire
        $objetId = $request->request->get('objet');
        $objet = $objetRepository->find($objetId);

        if (!$objet) {
            $this->addFlash('error', 'Objet non trouvé.');
            return $this->redirectToRoute('recyclage_edit', ['id' => $recyclage->getIdRecyclage()]);
        }

        // Mise à jour des champs
        $recyclage->setObjet($objet);
        $recyclage->setTypeRecyclage($request->request->get('typeRecyclage'));
        $recyclage->setCommentaire($request->request->get('commentaire'));

        // Sauvegarde des modifications
        $entityManager->flush();

        $this->addFlash('success', 'Recyclage mis à jour avec succès.');
        return $this->redirectToRoute('recyclage_index');
    }

    // Récupérer tous les objets pour le formulaire
    $objets = $objetRepository->findAll();

    return $this->render('recyclage/edit.html.twig', [
        'recyclage' => $recyclage,
        'objets' => $objets
    ]);
}
*/

#[Route('/recyclages/{id}/edit', name: 'recyclage_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Recyclage $recyclage, EntityManagerInterface $entityManager, ObjetRepository $objetRepository): Response
{
    // Vérifie si le recyclage existe
    if (!$recyclage) {
        $this->addFlash('error', 'Recyclage non trouvé.');
        return $this->redirectToRoute('recyclage_index');
    }

    if ($request->isMethod('POST')) {
        // Récupère l'ID de l'objet depuis le formulaire
        $objetId = $request->request->get('objet');
        $objet = $objetRepository->find($objetId);

        if (!$objet) {
            $this->addFlash('error', 'Objet non trouvé.');
            return $this->redirectToRoute('recyclage_edit', ['id' => $recyclage->getIdRecyclage()]);
        }

        // Mise à jour des données du recyclage
        $recyclage->setObjet($objet);
        $recyclage->setTypeRecyclage($request->request->get('typeRecyclage'));
        $recyclage->setCommentaire($request->request->get('commentaire'));

        // Sauvegarde en base de données
        $entityManager->persist($recyclage);
        $entityManager->flush();

        $this->addFlash('success', 'Recyclage mis à jour avec succès.');
        return $this->redirectToRoute('recyclage_index');
    }

    // Récupère tous les objets pour la liste déroulante
    $objets = $objetRepository->findAll();

    return $this->render('recyclage/edit.html.twig', [
        'recyclage' => $recyclage,
        'objets' => $objets,
    ]);
}


    // Route pour supprimer un recyclage
    #[Route('/recyclages/{id}/delete', name: 'recyclage_delete', methods: ['POST'])]
    public function delete(Request $request, Recyclage $recyclage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $recyclage->getIdRecyclage(), $request->request->get('_token'))) {
            $entityManager->remove($recyclage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('recyclage_index');
    }

    #[Route('/recyclage/new', name: 'recyclage_new')]
    public function new(Request $request, EntityManagerInterface $em, UtilisateurRepository $utilisateurRepo, ObjetRepository $objetRepo): Response
    {
        $recyclage = new Recyclage();

        // Récupérer tous les utilisateurs et objets
        $utilisateurs = $utilisateurRepo->findAll();
        $objets = $objetRepo->findAll();

        if ($request->isMethod('POST')) {
            $idUtilisateur = $request->request->get('utilisateur');
            $idObjet = $request->request->get('objet');
            $typeRecyclage = $request->request->get('typeRecyclage');
            $commentaire = $request->request->get('commentaire');
            $dateRecyclage = new \DateTime(); // Date actuelle

            $utilisateur = $utilisateurRepo->find($idUtilisateur);
            $objet = $objetRepo->find($idObjet);

            if ($utilisateur && $objet) {
                $recyclage->setUtilisateur($utilisateur)
                          ->setObjet($objet)
                          ->setTypeRecyclage($typeRecyclage)
                          ->setCommentaire($commentaire)
                          ->setDateRecyclage($dateRecyclage);

                $em->persist($recyclage);
                $em->flush();

                $this->addFlash('success', 'Recyclage ajouté avec succès !');
                return $this->redirectToRoute('recyclage_index');
            } else {
                $this->addFlash('error', 'Utilisateur ou Objet non trouvé.');
            }
        }

        // Transmettre les utilisateurs et objets au template
        return $this->render('recyclage/new.html.twig', [
            'utilisateurs' => $utilisateurs,
            'objets' => $objets,
        ]);
    }
    
    

}
