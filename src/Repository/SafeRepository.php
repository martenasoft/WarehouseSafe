<?php

namespace MartenaSoft\WarehouseSafe\Repository;

use MartenaSoft\WarehouseSafe\Entity\Safe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class SafeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Safe::class);
    }

    public function getItemsToChoice(): array
    {
        $ret = [];

        foreach ($this->createQueryBuilder('s')->getQuery()->getArrayResult() as $item) {
            $ret[$item['name']] = $item['id'];
        }

        return $ret;
    }

    public function getLastSum(): int
    {
        return (int)$this
            ->createQueryBuilder('s')
            ->select('s.sum')
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getScalarResult()
            ;
    }
}
