<?php

namespace App\Repository;

use App\Entity\Seller;
use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicle>
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    /**
     * Méthode permet de récuperer les annones d'un vendeur
     *
     * @param Seller $seller
     * @return array
     */
    public function findAnnonces(Seller $seller): array
    {
        return $this->createQueryBuilder('v')
        ->where('v.seller = :seller')
        ->setParameter('seller', $seller)
        ->orderBy('v.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
    }

    /**
     * Méthode permet de récuperer les annonces active.
     *
     * @return array
     */
    public function findActiveAnnonces(): array
    {
        return $this->createQueryBuilder('v')
        ->where('v.status = :status')
        ->setParameter('status', 'active')
        ->orderBy('v.createdAt', 'DESC')
        ->setMaxResults(4)
        ->getQuery()
        ->getResult();
    }
}
