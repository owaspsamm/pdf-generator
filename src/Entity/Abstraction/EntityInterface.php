<?php
/**
 *
 * PHP version 8
 *
 * @category PHP
 * @package  Admin
 * @author   CODIFIC <info@codific.com>
 * @link     http://codific.com
 */

declare(strict_types=1);


namespace App\Entity\Abstraction;

interface EntityInterface
{

    public function getId(): ?int;

    public function setId(?int $id);

    public function getCreatedAt(): ?\DateTime;

    public function setCreatedAt();

    public function getUpdatedAt(): ?\DateTime;

    public function setUpdatedAt();

    public function getDeletedAt(): ?\DateTime;

    public function setDeletedAt(?\DateTime $deletedAt);
    
    public function __toString();
    
}
