<?php

namespace App\Repository;

use App\Entity\Recyclage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recyclage>
 */
class RecyclageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recyclage::class);
    }

    // Méthode personnalisée pour récupérer les recyclages
    public function findAllRecyclages(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.date_recyclage', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByTypeRecyclage(?string $typeRecyclage): array
{
    $qb = $this->createQueryBuilder('r');

    if ($typeRecyclage) {
        $qb->andWhere('r.type_recyclage = :type')
        ->setParameter('type', $typeRecyclage);
     
    }

    return $qb->getQuery()->getResult();
}

}
