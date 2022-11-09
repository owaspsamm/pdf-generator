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

//#BlockStart number=84 id=_19_0_3_40d01a2_1635864815057_862891_6258_#_0

use App\Entity\PracticeLevel;
use App\Pagination\Paginator;
use App\Repository\Abstraction\AbstractRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method PracticeLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method PracticeLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method PracticeLevel[]    findAll()
 * @method PracticeLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PracticeLevelRepository extends SammCoreEntitiesAbstractRepository
{

    /**
     * PracticeLevelRepository constructor.
     * @param ManagerRegistry $registry
     * @param string $entityClassName
     * @return void
     */
    public function __construct(ManagerRegistry $registry, string $entityClassName = PracticeLevel::class)
    {
        parent::__construct($registry, $entityClassName);
    }

    /**
     * Duplicate the object and save the duplicate
     * @param PracticeLevel $practiceLevel The object to be duplicated
     * @return PracticeLevel
     */
    public function duplicate(PracticeLevel $practiceLevel): PracticeLevel
    {
        $clone = $practiceLevel->getCopy();
        $this->getEntityManager()->persist($clone);
        $this->getEntityManager()->flush();

        return $clone;
    }
    
//#BlockEnd number=84

}
