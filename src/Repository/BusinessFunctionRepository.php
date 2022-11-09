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

//#BlockStart number=48 id=_19_0_3_40d01a2_1635864197250_327408_5981_#_0

use App\Entity\BusinessFunction;
use App\Pagination\Paginator;
use App\Repository\Abstraction\AbstractRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method BusinessFunction|null find($id, $lockMode = null, $lockVersion = null)
 * @method BusinessFunction|null findOneBy(array $criteria, array $orderBy = null)
 * @method BusinessFunction[]    findAll()
 * @method BusinessFunction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BusinessFunctionRepository extends SammCoreEntitiesAbstractRepository
{

    /**
     * BusinessFunctionRepository constructor.
     * @param ManagerRegistry $registry
     * @param string $entityClassName
     * @return void
     */
    public function __construct(ManagerRegistry $registry, string $entityClassName = BusinessFunction::class)
    {
        parent::__construct($registry, $entityClassName);
    }

    /**
     * Duplicate the object and save the duplicate
     * @param BusinessFunction $businessFunction The object to be duplicated
     * @return BusinessFunction
     */
    public function duplicate(BusinessFunction $businessFunction): BusinessFunction
    {
        $clone = $businessFunction->getCopy();
        $this->getEntityManager()->persist($clone);
        $this->getEntityManager()->flush();

        return $clone;
    }


    /**
     * @return BusinessFunction[]
     */
    public function findOptimized(): array
    {
        $qb = $this->createQueryBuilder('business_function')
            ->leftJoin('business_function.businessFunctionPractices', '_practice')
            ->addSelect('_practice')
            ->leftJoin('_practice.practiceStreams', '_stream')
            ->addSelect('_stream')
            ->join('_practice.practicePracticeLevels', 'practice_level')
            ->addSelect('practice_level')
            ->join('_stream.streamActivities', '_activity')
            ->addSelect('_activity')
            ->join('_activity.activityQuestions', '_question')
            ->addSelect('_question')
            ->join('_question.answerSet', 'answer_set')
            ->addSelect('answer_set')
            ->join('answer_set.answerSetAnswers', 'answer')
            ->addSelect('answer')
            ->orderBy('business_function.order', 'ASC');
        return $qb->getQuery()->getResult();
    }
    
//#BlockEnd number=48

}
