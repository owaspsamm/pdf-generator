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


namespace App\Entity\Abstraction;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Serializer\Annotation\Ignore;

abstract class AbstractEntity implements EntityInterface
{

    #[Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    protected ?int $id = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected ?\DateTime $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected ?\DateTime $updatedAt = null;

    #[ORM\Column(name: 'deleted_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTime $deletedAt = null;

    /**
     * Get id attribute
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set id attribute
     * @param int|null $id the id attribute value
     * @return AbstractEntity
     */
    public function setId(?int $id): AbstractEntity
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get createdAt attribute
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt attribute
     * @return void
     * @throws \Exception
     */
    #[Orm\PrePersist]
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime("NOW");
    }

    /**
     * Get updatedAt attribute
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedAt attribute
     * @return void
     * @throws \Exception
     */
    #[Orm\PrePersist]
    #[Orm\PreUpdate]
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime("NOW");
    }

    /**
     * Get deletedAt attribute
     * @return \DateTime|null
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    /**
     * Set deletedAt attribute
     * @param \DateTime|null $deletedAt the deletedAt attribute value
     * @return void
     */
    public function setDeletedAt(?\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * Returns whether the entity is deleted or not
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deletedAt != null;
    }

    /**
     * Get name
     * @param bool $camelCase
     * @return string
     */
    public function getEntityName(bool $camelCase = false): string
    {
        if ($camelCase) {
            $temp = explode("\\", get_class($this));

            return lcfirst(end($temp));
        } else {
            $temp = explode("\\", strtolower(get_class($this)));

            return end($temp);
        }
    }

    /**
     * Get underscore name
     * @param string|null $propertyName
     * @return string
     */
    public function getUnderscoreEntityName(?string $propertyName = null): string
    {
        $temp = explode("\\", get_class($this));
        if ($propertyName != null) {
            $temp = explode("\\", $propertyName);
        }

        return trim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', end($temp))), "_");
    }

    /**
     * Get query builder alias
     * @param string|null $propertyName
     * @return string
     */
    public function getAliasName(?string $propertyName = null): string
    {
        return "_".$this->getUnderscoreEntityName($propertyName);
    }

    /**
     * Get the fields for less purifying
     * @return array
     */
    public function getLessPurifiedFields(): array
    {
        return [];
    }

    public function getUploadFields(): array
    {
        return [];
    }

    public function getReadOnlyFields(): array
    {
        return [];
    }

    public function getGeneratedFilterFields(): array
    {
        return [];
    }

    public function getFilterFields(): array
    {
        return $this->getGeneratedFilterFields();
    }

    public function getParentClasses(): array
    {
        return [];
    }
}