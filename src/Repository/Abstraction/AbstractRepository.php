<?php
/**
 * This is automatically generated file using the Codific Prototizer
 *
 * PHP version 8
 *
 * @category PHP
 * @package  Admin
 * @author   CODIFIC <info@codific.com>
 * @link     http://codific.com
 */

declare(strict_types=1);


namespace App\Repository\Abstraction;

use App\Entity\Abstraction\AbstractEntity;
use App\Entity\Abstraction\EntityInterface;
use App\Pagination\Paginator;
use App\Utils\AbstractRepositoryParameters;
use App\Utils\RepositoryParameters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;


abstract class AbstractRepository extends ServiceEntityRepository
{

    public static int $defaultPage = 1;

    /**
     * AbstractRepository constructor.
     * @param ManagerRegistry $registry
     * @param string $entityClassName
     * @return void
     */
    public function __construct(ManagerRegistry $registry, string $entityClassName)
    {
        parent::__construct($registry, $entityClassName);
        $config = $this->getEntityManager()->getConfiguration();
        $config->addCustomNumericFunction('STRING', 'App\Query\CastForLike');
    }

    /**
     * Delete the object by the given id from the database
     * @param EntityInterface $model the object to be deleted
     * @param boolean $forceDelete a flag that indicates whether this object should be definitively deleted (no trash)
     * @return void
     */
    public function delete(EntityInterface $model, bool $forceDelete = false)
    {
        if ($forceDelete) {
            $this->getEntityManager()->remove($model);
            $this->getEntityManager()->flush();
        } else {
            $this->trash($model);
        }
    }

    /**
     * Deletes the object
     * @param EntityInterface $model the object to be trashed
     * @return void
     */
    public function trash(EntityInterface $model): void
    {
        $reflection = $this->getClassMetadata()->newInstance();
        //soft delete all classes associated with this one
        foreach ($reflection::$childProperties as $childProperty => $parentProperty) {
            foreach ($model->{"get".ucfirst($childProperty)}() as $entity) {
                /** @var AbstractRepository $repository */
                $repository = $this->getEntityManager()->getRepository(get_class($entity));
                $repository->trash($entity);
                $this->getEntityManager()->persist($entity);
            }
        }
        //hard delete all many-to-many classes
        foreach ($reflection::$manyToManyProperties as $collectionProperty => $parentProperty) {
            foreach ($model->{"get".ucfirst($collectionProperty)}() as $entity) {
                $this->getEntityManager()->remove($entity);
            }
        }
        $model->setDeletedAt(new \DateTime("NOW"));
        $this->getEntityManager()->flush();
    }

    /**
     * Restores the deleted status of this object
     * @param EntityInterface $model the object to be restored
     * @return void
     */
    public function restore(EntityInterface $model)
    {
        $model->setDeletedAt(null);
        $this->getEntityManager()->flush();
    }

    /**
     * Get paginated list
     * @param QueryBuilder $queryBuilder
     * @param RepositoryParameters $repositoryParameters
     * @return Paginator
     */
    public function getPaginatedList(QueryBuilder $queryBuilder, RepositoryParameters $repositoryParameters): Paginator
    {

        $page = $repositoryParameters->getPage() ?? self::$defaultPage;
        $entityName = $this->getEntityName();
        /** @var AbstractEntity $entityName */
        $entityInstance = new $entityName();
        $filterFields = $entityInstance->getFilterFields();
        $queryBuilder = $this->generateSearchQuery($queryBuilder, $repositoryParameters, $filterFields);

        if (!$this->getEntityManager()->getFilters()->isEnabled("deleted_entity")) {
            $this->getEntityManager()->getFilters()->enable("deleted_entity");
        }
        $filter = $this->getEntityManager()->getFilters()->getFilter("deleted_entity");
        $filter->setParameter("deleted", $repositoryParameters->getShowDeleted());

        if (count($repositoryParameters->getOrderBy()) == 0) {
            $repositoryParameters->setOrderBy(
                [[AbstractRepositoryParameters::$defaultOrderColumn.' '.AbstractRepositoryParameters::$defaultOrderDirection]]
            );
        }
        foreach ($repositoryParameters->getOrderBy() as $order) {
            $orderColumn = $order[0] ?? '';
            $orderDirection = $order[1] ?? '';
            if (property_exists($entityName, $orderColumn)) {
                $queryBuilder->addOrderBy($this->getClassMetadata()->newInstance()->getAliasName().".".$orderColumn, $orderDirection);
            } else {
                if (str_contains($orderColumn, '.')) {
                    $queryBuilder->addOrderBy($orderColumn, $orderDirection);
                }
            }
        }

        return (new Paginator($queryBuilder, $repositoryParameters->getPageSize()))->paginate($page);
    }

    /**
     * Generates where clause for the advanced search
     * @param QueryBuilder $queryBuilder Doctrine Query builder
     * @param RepositoryParameters $repositoryParameters
     * @param array $fields array of columns that should be matched against the $escapeSearch string
     * @return QueryBuilder
     */
    protected function generateSearchQuery(QueryBuilder $queryBuilder, RepositoryParameters $repositoryParameters, array $fields = []): QueryBuilder
    {
        $search = $repositoryParameters->getFilter();
        if ($search != null) {
            $searchWhere = "";
            $searchArray = array_filter(explode("+", $search));
            foreach ($searchArray as $i => $searchString) {
                $subQuery = "";
                foreach ($fields as $searchField) {
                    $subQuery .= "$searchField LIKE :searchString$i OR ";
                }
                foreach ($repositoryParameters->getAdditionalSearchFields() as $searchField) {
                    $subQuery .= "$searchField LIKE :searchString$i OR ";
                }
                $searchWhere .= $subQuery;
                $queryBuilder->setParameter("searchString$i", "%".trim($searchString)."%");
            }
            $andWhere = trim($searchWhere, " OR ");
            $queryBuilder->andWhere("(".$andWhere.")");
        }

        return $queryBuilder;
    }

    /**
     * Returns a collection of the supplied entity class
     * filtered and ordered by the supplied repository parameters
     * @param RepositoryParameters $repositoryParameters
     * @return array|null
     */
    public function getSearchResult(RepositoryParameters $repositoryParameters): ?array
    {
        /** @var AbstractEntity $reflection */
        $reflection = $this->getClassMetadata()->newInstance();
        $queryBuilder = $this->createQueryBuilder($reflection->getAliasName());
        $queryBuilder = $this->generateSearchQuery($queryBuilder, $repositoryParameters, $reflection->getFilterFields());
        foreach ($repositoryParameters->getOrderBy() as $order) {
            $queryBuilder->addOrderBy($order[0], $order[1]);
        }

        return $queryBuilder->getQuery()->getResult();
    }

}