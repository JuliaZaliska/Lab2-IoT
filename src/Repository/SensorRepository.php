<?php

namespace App\Repository;

use App\Entity\Sensor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sensor>
 */
class SensorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sensor::class);
    }

    /**
     * @return Sensor[]
     */
    public function findFiltered(?string $query, ?int $locationId, ?string $status, bool $onlyAlerts = false): array
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.location', 'l')
            ->addSelect('l')
            ->orderBy('s.id', 'DESC');

        if ($query !== null && trim($query) !== '') {
            $qb->andWhere('LOWER(s.name) LIKE :q OR LOWER(s.sensorType) LIKE :q OR LOWER(l.name) LIKE :q')
               ->setParameter('q', '%' . mb_strtolower(trim($query)) . '%');
        }

        if ($locationId !== null && $locationId > 0) {
            $qb->andWhere('l.id = :locationId')
               ->setParameter('locationId', $locationId);
        }

        if ($status !== null && in_array($status, ['online', 'offline', 'maintenance'], true)) {
            $qb->andWhere('s.status = :status')
               ->setParameter('status', $status);
        }

        if ($onlyAlerts) {
            $qb->andWhere('s.status = :onlineStatus')
               ->andWhere('s.alertThreshold IS NOT NULL')
               ->andWhere('s.currentValue >= s.alertThreshold')
               ->setParameter('onlineStatus', 'online');
        }

        return $qb->getQuery()->getResult();
    }
}