<?php

namespace App\Utils;

use Doctrine\ORM\QueryBuilder;

final class IndexViewParameters
{

    private ?QueryBuilder $queryBuilder = null;

    private ?RepositoryParameters $repositoryParameters = null;

    private array $viewParameters = [];

    private array $queryParams = [];

    private string $entityName = "";

    private string $entityCamelCaseName = "";

    /**
     * @return QueryBuilder|null
     */
    public function getQueryBuilder(): ?QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * @param QueryBuilder|null $queryBuilder
     * @return IndexViewParameters
     */
    public function setQueryBuilder(?QueryBuilder $queryBuilder): IndexViewParameters
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    /**
     * @return RepositoryParameters|null
     */
    public function getRepositoryParameters(): ?RepositoryParameters
    {
        return $this->repositoryParameters;
    }

    /**
     * @param RepositoryParameters|null $repositoryParameters
     * @return IndexViewParameters
     */
    public function setRepositoryParameters(?RepositoryParameters $repositoryParameters): IndexViewParameters
    {
        $this->repositoryParameters = $repositoryParameters;

        return $this;
    }

    /**
     * @return array
     */
    public function getViewParameters(): array
    {
        return $this->viewParameters;
    }

    /**
     * @param array $viewParameters
     * @return IndexViewParameters
     */
    public function setViewParameters(array $viewParameters): IndexViewParameters
    {
        $this->viewParameters = $viewParameters;

        return $this;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @param array $queryParams
     * @return IndexViewParameters
     */
    public function setQueryParams(array $queryParams): IndexViewParameters
    {
        $this->queryParams = $queryParams;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * @param string $entityName
     * @return IndexViewParameters
     */
    public function setEntityName(string $entityName): IndexViewParameters
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityCamelCaseName(): string
    {
        return $this->entityCamelCaseName;
    }

    /**
     * @param string $entityCamelCaseName
     * @return IndexViewParameters
     */
    public function setEntityCamelCaseName(string $entityCamelCaseName): IndexViewParameters
    {
        $this->entityCamelCaseName = $entityCamelCaseName;

        return $this;
    }

}