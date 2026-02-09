<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    //    /**
    //     * @return Activity[] Returns an array of Activity objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    /**
     * Find activities (optionally by category and price range) with schedules and guide loaded.
     *
     * @return Activity[]
     */
    public function findAllForBlog(?int $categoryId = null, ?float $minPrice = null, ?float $maxPrice = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'c')
            ->leftJoin('a.schedules', 's')
            ->leftJoin('a.guide', 'g')
            ->addSelect('c', 's', 'g')
<<<<<<< HEAD
=======
            ->where('a.isActive = :active')
            ->setParameter('active', true)
>>>>>>> f5ab5f2b8143340c9833c9379b76af33954bf087
            ->orderBy('a.title', 'ASC');

        if ($categoryId !== null) {
            $qb->andWhere('c.id = :categoryId')
               ->setParameter('categoryId', $categoryId);
        }
        if ($minPrice !== null) {
            $qb->andWhere('a.price >= :minPrice')->setParameter('minPrice', $minPrice);
        }
        if ($maxPrice !== null) {
            $qb->andWhere('a.price <= :maxPrice')->setParameter('maxPrice', $maxPrice);
        }

        return $qb->getQuery()->getResult();
    }
<<<<<<< HEAD
=======

    /**
     * Find activities filtered by price range only
     *
     * @return Activity[]
     */
    public function findByPriceRange(?float $minPrice = null, ?float $maxPrice = null): array
    {
        return $this->findAllForBlog(null, $minPrice, $maxPrice);
    }

    /**
     * Find activities filtered by category and optional price range
     *
     * @return Activity[]
     */
    public function findByCategoryAndPrice(?int $categoryId = null, ?float $minPrice = null, ?float $maxPrice = null): array
    {
        return $this->findAllForBlog($categoryId, $minPrice, $maxPrice);
    }
>>>>>>> f5ab5f2b8143340c9833c9379b76af33954bf087
}
