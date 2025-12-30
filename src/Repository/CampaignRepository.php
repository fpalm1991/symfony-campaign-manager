<?php

namespace App\Repository;

use App\Entity\Campaign;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Campaign>
 */
class CampaignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campaign::class);
    }

    public function findAllActiveCampaigns(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.lifecycle = :lifecycle')
            ->setParameter('lifecycle', 'active')
            ->getQuery()
            ->getResult();
    }

    public function findAllArchivedCampaigns(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.lifecycle = :lifecycle')
            ->setParameter('lifecycle', 'archived')
            ->getQuery()
            ->getResult();
    }

    public function findAllMyActiveCampaigns(User $user): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->andWhere(
                $qb->expr()->orX('c.project_manager = :user', 'c.campaign_owner = :user')
            )
            ->andWhere('c.lifecycle = :lifecycle')
            ->setParameter('user', $user)
            ->setParameter('lifecycle', 'active')
            ->getQuery()
            ->getResult();
    }

    public function campaignsEndingThisMonth(): array
    {
        $start = new \DateTimeImmutable('first day of this month 00:00:00');
        $end = new \DateTimeImmutable('first day of next month 00:00:00');

        return $this->createQueryBuilder('c')
            ->andWhere('c.end_date >= :start')
            ->andWhere('c.end_date < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('c.end_date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Campaign[] Returns an array of Campaign objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Campaign
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
