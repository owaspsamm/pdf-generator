<?php

declare(strict_types=1);

namespace App\Repository;

use App\Repository\Abstraction\AbstractRepository;

abstract class SammCoreEntitiesAbstractRepository extends AbstractRepository
{
    /**
     * @param array $externalIds
     * @return mixed
     */
    public function findByExternalIdsNotIn(array $externalIds): mixed
    {
        return $this->createQueryBuilder('_entity')
            ->where('_entity.externalId NOT IN (:externalIds)')
            ->setParameter('externalIds', $externalIds)
            ->getQuery()
            ->getResult();
    }
}