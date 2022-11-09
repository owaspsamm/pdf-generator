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

namespace App\Utils;


use Doctrine\ORM\QueryBuilder;

final class ViewParameters extends AbstractRepositoryParameters
{

    private array $queryParams = [];

    private ?QueryBuilder $queryBuilder = null;

    private array $filters = [];

    private ?string $searchFormTwigPath = null;

    private array $searchFormTooltipFields = [];

    private array $joins = [];

    private array $order = [];

    /** @var \StdClass|null $additionalViewVars Any additional properties which have to be sent to the view */
    public ?\StdClass $additionalViewVars = null;

    /**
     * ViewParameters constructor.
     */
    public function __construct()
    {
        $this->additionalViewVars = new \StdClass();
    }

    /**
     * Get the url query params
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * Set additional url query parameters that will be used for creating the paginator
     *
     * @param array $queryParams
     * @return $this
     */
    public function setQueryParams(array $queryParams): self
    {
        $this->queryParams = $queryParams;

        return $this;
    }

    /**
     * Get the predefined Query builder
     * @return QueryBuilder|null
     */
    public function getQueryBuilder(): ?QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * Override the default query builder by some custom query builder you have in a certain repository
     * e.g., setQueryBuilder($myRepository->getMyCustomQueryBuilder())
     * @param QueryBuilder|null $queryBuilder
     * @return $this
     */
    public function setQueryBuilder(?QueryBuilder $queryBuilder): self
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    /**
     * Get the additional filters
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Set additional filters that will be always used in the query builder to filter a specific subset
     * e.g., setFilters(["type" => MyTypeEnum::TYPE_1])
     * @param array $filters
     * @return $this
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Get the search form override file
     * @return string|null
     */
    public function getSearchFormTwigPath(): ?string
    {
        return $this->searchFormTwigPath;
    }

    /**
     * Change the default search form twig from _search_form.html.twig to something else
     * e.g., setSearchFormTwigPath("admin/my_class/_my_search_form.html.twig") to use templates/admin/my_class/_my_search_form.html.twig
     * @param string|null $searchFormTwigPath
     * @return $this
     */
    public function setSearchFormTwigPath(?string $searchFormTwigPath): self
    {
        $this->searchFormTwigPath = $searchFormTwigPath;

        return $this;
    }

    /**
     * Get the search tooltip fields
     * @return array
     */
    public function getSearchFormTooltipFields(): array
    {
        return $this->searchFormTooltipFields;
    }

    /**
     * Override the default search form tooltip fields
     * e.g.,
     * setSearchFormTooltipFields("name", "surname");
     * setSearchFormTooltipFields($this->translator->trans('admin.internship.trainers'), $this->translator->trans('admin.internship.organisation'));
     * @param string ...$searchFormTooltipFields
     * @return $this
     */
    public function setSearchFormTooltipFields(string ...$searchFormTooltipFields): self
    {
        $this->searchFormTooltipFields = array_merge($this->searchFormTooltipFields, $searchFormTooltipFields);

        return $this;
    }

    /**
     * Add join and select
     * @param string $with
     * @param string $property
     * @param string $type
     * @return $this
     */
    public function addJoin(string $with, string $property, string $type = "inner"): self
    {
        $this->joins[] = ['with' => $with, 'property' => $property, 'type' => $type];

        return $this;
    }

    /**
     * Add order by
     * @param string $orderColumn
     * @param string $orderDirection
     * @return $this
     */
    public function addOrderBy(string $orderColumn, string $orderDirection): self
    {
        $this->order[] = [$orderColumn, $orderDirection];

        return $this;
    }

    /**
     * Get array with only the modified properties
     * @return array
     */
    public function getModifiedPropertiesArray(): array
    {
        try {
            foreach (get_object_vars($this->additionalViewVars) as $key => $value) {
                if (!property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
            $diff = $this->arrayRecursiveDiff(get_object_vars($this), (new \ReflectionClass($this))->getDefaultProperties());
            if (isset($diff['additionalViewVars'])) {
                unset($diff['additionalViewVars']);
            }

            return $diff;
        } catch (\ReflectionException $e) {
            return [];
        }
    }

    /**
     * Recursive diff between two arrays
     * @param array $firstArray
     * @param array $secondArray
     * @return array
     */
    private function arrayRecursiveDiff(array $firstArray, array $secondArray): array
    {
        $result = [];
        foreach ($firstArray as $key => $value) {
            if (array_key_exists($key, $secondArray)) {
                if (is_array($value)) {
                    $recursiveDiff = $this->arrayRecursiveDiff($value, $secondArray[$key]);
                    if (count($recursiveDiff) > 0) {
                        $result[$key] = $recursiveDiff;
                    }
                } else {
                    if ($value != $secondArray[$key]) {
                        $result[$key] = $value;
                    }
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

}