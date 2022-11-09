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

//#BlockStart number=102 id=_19_0_3_40d01a2_1635864957642_388856_6397_#_0

use App\Entity\Practice;
use App\Entity\Question;
use App\Entity\Stream;
use App\Pagination\Paginator;
use App\Repository\Abstraction\AbstractRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends SammCoreEntitiesAbstractRepository
{

    /**
     * QuestionRepository constructor.
     * @param ManagerRegistry $registry
     * @param string $entityClassName
     * @return void
     */
    public function __construct(ManagerRegistry $registry, string $entityClassName = Question::class)
    {
        parent::__construct($registry, $entityClassName);
    }

    /**
     * Duplicate the object and save the duplicate
     * @param Question $question The object to be duplicated
     * @return Question
     */
    public function duplicate(Question $question): Question
    {
        $clone = $question->getCopy();
        $this->getEntityManager()->persist($clone);
        $this->getEntityManager()->flush();

        return $clone;
    }

    public function findAllQuestionsFullChain(): array
    {
        return $this->createQueryBuilder('question')
            ->join('question.answerSet', 'answerSet')
            ->addSelect('answerSet')
            ->join('question.activity', 'activity')
            ->addSelect('activity')
            ->join('activity.practiceLevel', 'practiceLevel')
            ->addSelect('practiceLevel')
            ->join('practiceLevel.maturityLevel', 'maturityLevel')
            ->addSelect('maturityLevel')
            ->join('activity.stream', 'stream')
            ->addSelect('stream')
            ->join('stream.practice', 'practice')
            ->addSelect('practice')
            ->join('practice.businessFunction', 'businessFunction')
            ->addSelect('businessFunction')
            ->getQuery()->getResult();
    }

//#BlockEnd number=102

}
