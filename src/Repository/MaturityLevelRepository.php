<?php

/**
 * This is automatically generated file using the Codific Prototizer
 * PHP version 8
 * @category PHP
 * @package  Admin
 * @author   CODIFIC <info@codific.com>
 * @link     http://codific.com
 */

declare(strict_types=1);

namespace App\Repository;

//#BlockStart number=75 id=_19_0_3_40d01a2_1635864718825_476062_6224_#_0

use App\Entity\MaturityLevel;
use App\Pagination\Paginator;
use App\Repository\Abstraction\AbstractRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method MaturityLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method MaturityLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method MaturityLevel[]    findAll()
 * @method MaturityLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaturityLevelRepository extends SammCoreEntitiesAbstractRepository
{

    /**
     * MaturityLevelRepository constructor.
     * @param ManagerRegistry $registry
     * @param string $entityClassName
     * @return void
     */
    public function __construct(ManagerRegistry $registry, string $entityClassName = MaturityLevel::class)
    {
        parent::__construct($registry, $entityClassName);
    }

    /**
     * Duplicate the object and save the duplicate
     * @param MaturityLevel $maturityLevel The object to be duplicated
     * @return MaturityLevel
     */
    public function duplicate(MaturityLevel $maturityLevel): MaturityLevel
    {
        $clone = $maturityLevel->getCopy();
        $this->getEntityManager()->persist($clone);
        $this->getEntityManager()->flush();

        return $clone;
    }

    /**
     * @return array
     */
    public function findAllIndexedByLevel(): array
    {
        return $this->createQueryBuilder("maturity_level", "maturity_level.level")
            ->getQuery()
            ->getResult();
    }
    
//#BlockEnd number=75

}
