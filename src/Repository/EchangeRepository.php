<?php

namespace App\Repository;

use App\Entity\Echange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Echange>
 *
 * @method Echange|null find($id, $lockMode = null, $lockVersion = null)
 * @method Echange|null findOneBy(array $criteria, array $orderBy = null)
 * @method Echange[]    findAll()
 * @method Echange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EchangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Echange::class);
    }

    /**
     * Find exchanges created within the last X hours
     * 
     * @param int $hours Number of hours to look back
     * @return Echange[] Returns an array of Echange objects
     */
    public function findRecentExchanges(int $hours): array
    {
        $date = new \DateTime();
        $date->modify('-' . $hours . ' hours');
        
        return $this->createQueryBuilder('e')
            ->andWhere('e.date_echange >= :date')
            ->setParameter('date', $date)
            ->orderBy('e.date_echange', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
