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

//#BlockStart number=57 id=_19_0_3_40d01a2_1635864210817_220463_6011_#_0

use App\Entity\Practice;
use App\Pagination\Paginator;
use App\Repository\Abstraction\AbstractRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method Practice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Practice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Practice[]    findAll()
 * @method Practice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PracticeRepository extends SammCoreEntitiesAbstractRepository
{

    /**
     * PracticeRepository constructor.
     * @param ManagerRegistry $registry
     * @param string $entityClassName
     * @return void
     */
    public function __construct(ManagerRegistry $registry, string $entityClassName = Practice::class)
    {
        parent::__construct($registry, $entityClassName);
    }

    /**
     * Duplicate the object and save the duplicate
     * @param Practice $practice The object to be duplicated
     * @return Practice
     */
    public function duplicate(Practice $practice): Practice
    {
        $clone = $practice->getCopy();
        $this->getEntityManager()->persist($clone);
        $this->getEntityManager()->flush();

        return $clone;
    }

    /**
     * @param string $externalId
     * @return Practice|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByExternalIdOptimized(string $externalId): ?Practice
    {
        $qb = $this->createQueryBuilder('_practice')
            ->join('_practice.practiceStreams', '_stream')
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
            ->where('_practice.externalId = :externalId')
            ->setParameter('externalId', $externalId);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return mixed
     */
    public function findAllOrderedByBusinessFunction(): mixed
    {
        return $this->createQueryBuilder("practice")
            ->join("practice.businessFunction", "businessFunction")
            ->addSelect("businessFunction")
            ->orderBy("businessFunction.order", "ASC")
            ->addOrderBy("practice.order", "ASC")
            ->getQuery()
            ->getResult();
    }
//#BlockEnd number=57

}
