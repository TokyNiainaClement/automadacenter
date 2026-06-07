<?php

namespace App\Repository;

use App\Entity\UserNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserNotification>
 */
class UserNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserNotification::class);
    }

    // public function findCountByUser(bool $isRead, ?User $user)
    // {
    //     return $this->createQueryBuilder('u')
    //     ->where('u.isRead = :val')
    //     ->andWhere('u.user = :user')
    //     ->setParameter('val', $isRead)
    //     ->setParameter('user', $user)
    //     ->getQuery()
    //     ->getResult();
    // }
}
