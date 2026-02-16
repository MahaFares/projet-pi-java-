<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Enum\ReservationType;
use App\Entity\Enum\ReservationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Find reservations by user
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find reservations by status
     */
    public function findByStatus(ReservationStatus $status): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.status = :status')
            ->setParameter('status', $status)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find reservations by type
     */
    public function findByType(ReservationType $type): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.reservationType = :type')
            ->setParameter('type', $type)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find user's reservations by type
     */
    public function findUserReservationsByType(int $userId, ReservationType $type): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.user = :userId')
            ->andWhere('r.reservationType = :type')
            ->setParameter('userId', $userId)
            ->setParameter('type', $type)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find pending reservations
     */
    public function findPendingReservations(): array
    {
        return $this->findByStatus(ReservationStatus::PENDING);
    }

    /**
     * Find confirmed reservations
     */
    public function findConfirmedReservations(): array
    {
        return $this->findByStatus(ReservationStatus::CONFIRMED);
    }

    /**
     * Count reservations by status
     */
    public function countByStatus(ReservationStatus $status): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue(): float
    {
        $result = $this->createQueryBuilder('r')
            ->select('SUM(r.totalPrice)')
            ->where('r.status = :status')
            ->setParameter('status', ReservationStatus::CONFIRMED)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ?? 0;
    }

    public function save(Reservation $reservation): void
    {
        $this->getEntityManager()->persist($reservation);
        $this->getEntityManager()->flush();
    }

    public function remove(Reservation $reservation): void
    {
        $this->getEntityManager()->remove($reservation);
        $this->getEntityManager()->flush();
    }
}
