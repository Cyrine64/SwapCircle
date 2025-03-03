<?php

namespace App\Repository;

use App\Entity\Tutorial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tutorial>
 */
class TutorialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tutorial::class);
    }

    // Méthode personnalisée pour récupérer tous les tutoriels
    public function findAllTutorials(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.date_creation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDescription(string $search): array
{
    return $this->createQueryBuilder('t')
        ->andWhere('t.description LIKE :search')
        ->setParameter('search', '%' . $search . '%')
        ->orderBy('t.id_tutorial', 'ASC')
        ->getQuery()
        ->getResult();
}

}
