<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Blog>
 *
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    public function save(Blog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Blog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Blog[] Returns an array of Blog objects ordered by date
     */
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.date_publication', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns top liked blogs first, then remaining blogs by date
     */
    public function findTopLikedThenByDate(int $topCount = 3): array
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.reactions', 'r')
            ->andWhere('r.type = :type')
            ->setParameter('type', 'like')
            ->groupBy('b.id_article')
            ->orderBy('COUNT(r.id)', 'DESC')
            ->setMaxResults($topCount);

        $topLikedBlogs = $qb->getQuery()->getResult();

        // Get IDs of top blogs to exclude them
        $topBlogIds = array_map(function($blog) {
            return $blog->getIdArticle();
        }, $topLikedBlogs);

        // Get remaining blogs ordered by date
        $remainingBlogs = $this->createQueryBuilder('b')
            ->where('b.id_article NOT IN (:ids)')
            ->setParameter('ids', $topBlogIds)
            ->orderBy('b.date_publication', 'DESC')
            ->getQuery()
            ->getResult();

        // Combine both results
        return array_merge($topLikedBlogs, $remainingBlogs);
    }

    public function findBySearch(?string $query)
    {
        $qb = $this->createQueryBuilder('b')
            ->orderBy('b.date_publication', 'DESC');

        if ($query) {
            $qb->andWhere('b.titre LIKE :query OR b.contenu LIKE :query')
               ->setParameter('query', '%' . $query . '%');
        }

        return $qb->getQuery();
    }
}
