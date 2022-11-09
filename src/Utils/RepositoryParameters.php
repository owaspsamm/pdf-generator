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


final class RepositoryParameters extends AbstractRepositoryParameters
{

    /**
     * Add order by
     * @param string[][] $orderBy Must be in format [[$orderColum, $orderDirection], [$orderColum, $orderDirection]]
     * @return $this
     */
    public function setOrderBy(array $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * Get order by
     * @return array
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

}
