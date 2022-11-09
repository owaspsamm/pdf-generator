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


abstract class AbstractRepositoryParameters
{

    public static string $defaultOrderColumn = 'id';

    public static string $defaultOrderDirection = 'DESC';

    protected ?string $filter = null;

    protected ?int $page = null;

    protected ?int $pageSize = null;

    protected bool $showDeleted = false;

    protected array $additionalSearchFields = [];

    protected array $orderBy = [];


    /**
     * Get the search field filter
     * @return string|null
     */
    public function getFilter(): ?string
    {
        return $this->filter;
    }

    /**
     * Set the search field filter
     * @param string|null $filter
     * @return $this
     */
    public function setFilter(?string $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Get the current paginator page
     * @return int|null
     */
    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * Set the current paginator page
     * @param int|null $page
     * @return $this
     */
    public function setPage(?int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get the pagination page size
     * @return int|null
     */
    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    /**
     * Set the pagination page size
     * @param int|null $pageSize
     * @return $this
     */
    public function setPageSize(?int $pageSize): self
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * Get if the deleted entities should be shown
     * @return bool
     */
    public function getShowDeleted(): bool
    {
        return $this->showDeleted;
    }

    /**
     * Set if the deleted entities should be shown
     * @param bool $showDeleted
     * @return $this
     */
    public function setShowDeleted(bool $showDeleted): self
    {
        $this->showDeleted = $showDeleted;

        return $this;
    }

    /**
     * @return array
     */
    public function getAdditionalSearchFields(): array
    {
        return $this->additionalSearchFields;
    }

    /**
     * @param string ...$names
     * @return $this
     */
    public function setAdditionalSearchFields(string ...$names): self
    {
        $this->additionalSearchFields = array_merge($this->additionalSearchFields, $names);

        return $this;
    }

}