<?php

namespace App\Repository;

use App\Entity\Reponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Objet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Objet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Objet[]    findAll()
 * @method Objet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reponse::class);
    }

    public function searchReponse(string $searchQuery)
    {
        return $this->createQueryBuilder('c')
            ->where('c.contenu LIKE :searchQuery')
            ->setParameter('searchQuery', '%' . $searchQuery . '%')
            ->getQuery()
            ->getResult();
    }

    public function countResponsesPerUser()
    {
            return $this->createQueryBuilder('r')
                ->select('u.id, COUNT(r.id_reponse) as response_count')
                ->leftJoin('r.utilisateur', 'u')
                ->groupBy('u.id')
                ->getQuery()
                ->getResult();
    }
    
    public function countResponsesPerReclamation()
    {
            return $this->createQueryBuilder('r')
                ->select('rec.id, COUNT(r.id_reponse) as response_count')
                ->leftJoin('r.reclamation', 'rec')
                ->groupBy('rec.id')
                ->getQuery()
                ->getResult();
    }
    
    public function averageResponseLength()
    {
            return $this->createQueryBuilder('r')
                ->select('AVG(LENGTH(r.contenu)) as average_length')
                ->getQuery()
                ->getSingleScalarResult();
    }
    
    public function countResponsesLastMonth()
    {
            return $this->createQueryBuilder('r')
                ->select('COUNT(r.id_reponse) as response_count')
                ->where('r.date_reponse > :last_month')
                ->setParameter('last_month', (new \DateTime())->modify('-1 month'))
                ->getQuery()
                ->getSingleScalarResult();
    }
} 