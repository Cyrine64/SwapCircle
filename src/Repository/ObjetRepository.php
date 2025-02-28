<?php

namespace App\Repository;

use App\Entity\Objet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

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

    public function findSortedAndFiltered(?string $search = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('o')
                   ->select('o');

        // Apply search filter if provided
        if ($search) {
            $searchTerms = array_filter(explode(' ', $search));
            $searchQuery = [];
            
            foreach ($searchTerms as $key => $term) {
                $searchQuery[] = '(LOWER(o.nom) LIKE LOWER(:search' . $key . ') 
                                OR LOWER(o.description) LIKE LOWER(:search' . $key . ')
                                OR LOWER(o.categorie) LIKE LOWER(:search' . $key . '))';
                $qb->setParameter('search' . $key, '%' . $term . '%');
            }
            
            if (!empty($searchQuery)) {
                $qb->andWhere(implode(' AND ', $searchQuery));
            }
        }

        return $qb;
    }

    public function getStatisticsData(): array
    {
        // Count by category
        $categoryStats = $this->createQueryBuilder('o')
            ->select('o.categorie as label, COUNT(o.id_objet) as count')
            ->groupBy('o.categorie')
            ->getQuery()
            ->getResult();

        // Count by status
        $statusStats = $this->createQueryBuilder('o')
            ->select('o.etat as label, COUNT(o.id_objet) as count')
            ->groupBy('o.etat')
            ->getQuery()
            ->getResult();

        // Objects added per month
        $monthlyStats = $this->createQueryBuilder('o')
            ->select('SUBSTRING(o.date_ajout, 1, 7) as month, COUNT(o.id_objet) as count')
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();

        return [
            'categoryStats' => $categoryStats,
            'statusStats' => $statusStats,
            'monthlyStats' => $monthlyStats
        ];
    }
}