<?php

namespace App\Repository;

use App\Entity\Objet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Objet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Objet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Objet[]    findAll()
 * @method Objet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Objet::class);
    }

    // Add custom methods here if needed

    public function findByMetier(?string $metier)
    {
        if (empty($metier)) {
            return [];
        }

        return $this->createQueryBuilder('o')
            ->andWhere('o.metier = :metier')
            ->setParameter('metier', $metier)
            ->orderBy('o.date_ajout', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getStatistiquesByMetier()
    {
        $qb = $this->createQueryBuilder('o');
        return $qb
            ->select('o.metier, COUNT(o.id_objet) as total')
            ->where($qb->expr()->isNotNull('o.metier'))
            ->andWhere($qb->expr()->neq('o.metier', ':empty'))
            ->setParameter('empty', '')
            ->groupBy('o.metier')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByMetierAndCategorie(?string $metier = null, ?string $categorie = null)
    {
        $qb = $this->createQueryBuilder('o');

        if (!empty($metier)) {
            $qb->andWhere('o.metier = :metier')
               ->setParameter('metier', $metier);
        }

        if (!empty($categorie)) {
            $qb->andWhere('o.categorie = :categorie')
               ->setParameter('categorie', $categorie);
        }

        if (empty($metier) && empty($categorie)) {
            return $this->findAll();
        }

        return $qb->orderBy('o.date_ajout', 'DESC')
                 ->getQuery()
                 ->getResult();
    }
} 