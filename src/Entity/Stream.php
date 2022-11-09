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

namespace App\Entity;

use App\Entity\Abstraction\AbstractEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Ignore;


//#BlockStart number=62 id=_19_0_3_40d01a2_1635864239167_130089_6058_#_0

//#BlockEnd number=62


#[ORM\Table(name: "`stream`")]
#[ORM\Entity(repositoryClass: "App\Repository\StreamRepository")]
#[ORM\HasLifecycleCallbacks]
class Stream extends AbstractEntity
{

    #[ORM\Column(name: "`name`", type: Types::STRING, nullable: true)]
    protected ?string $name = "";

    #[ORM\ManyToOne(targetEntity: Practice::class, cascade: ["persist"], fetch: "EAGER", inversedBy: "practiceStreams")]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    #[MaxDepth(1)]
    protected ?Practice $practice = null;

    #[ORM\Column(name: "`description`", type: Types::TEXT, nullable: true)]
    protected ?string $description = "";

    #[ORM\Column(name: "`order`", type: Types::INTEGER)]
    protected int $order = 0;

    #[ORM\Column(name: "`external_id`", type: Types::STRING, nullable: true)]
    protected ?string $externalId = "";

    #[ORM\Column(name: "`weight`", type: Types::DECIMAL, precision: 10, scale: 2)]
    protected float $weight = 0.0;

    #[ORM\OneToMany(mappedBy: "stream", targetEntity: Activity::class, cascade: ["persist"], fetch: "LAZY", orphanRemoval: false)]
    #[ORM\OrderBy(["id" => "ASC"])]
    #[MaxDepth(1)]
    protected Collection $streamActivities;

    public const STREAMLETTER_A = 1;
    public const STREAMLETTER_B = 2;

    /**
     * Stream constructor
     * @return void
     */
    public function __construct()
    {
        $this->streamActivities = new ArrayCollection();

    }

    /**
     * Set name
     * @param string|null $name the setter value
     * @return Stream
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set practice
     * @param Practice|null $practice the setter value
     * @return Stream
     */
    public function setPractice(?Practice $practice): self
    {
        $this->practice = $practice;

        return $this;
    }

    /**
     * Get practice
     * @return Practice|null
     */
    public function getPractice(): ?Practice
    {
        return $this->practice;
    }

    /**
     * Set description
     * @param string|null $description the setter value
     * @return Stream
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set order
     * @param int $order the setter value
     * @return Stream
     */
    public function setOrder(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * Set externalId
     * @param string|null $externalId the setter value
     * @return Stream
     */
    public function setExternalId(?string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * Get externalId
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    /**
     * Set weight
     * @param float $weight the setter value
     * @return Stream
     */
    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * Get Stream Activities
     * @return Collection<Activity>
     */
    public function getStreamActivities(): Collection
    {
        return $this->streamActivities;
    }

    /**
     * Add Activities Activity
     * @param Activity $activity
     * @return Stream
     */
    public function addStreamActivity(Activity $activity): Stream
    {
        $this->streamActivities->add($activity);

        return $this;
    }


    /**
     * This method is a copy constructor that will return a copy object (except for the id field)
     * Note that this method will not save the object
     * @param Stream|null $clone a clone object that is either null or already partially initialized
     * @return Stream
     */
    #[Ignore]
    public function getCopy(?Stream $clone = null): Stream
    {
        if ($clone == null) {
            $clone = new Stream();
        }
        $clone->setName($this->name);
        $clone->setPractice($this->practice);
        $clone->setDescription($this->description);
        $clone->setOrder($this->order);
        $clone->setExternalId($this->externalId);
        $clone->setWeight($this->weight);
//#BlockStart number=63 id=_19_0_3_40d01a2_1635864239167_130089_6058_#_1

//#BlockEnd number=63

        return $clone;
    }

    /**
     * Private to string method auto generated based on the UML properties
     * This is the new way of doing things.
     * @return string
     */
    public function toString(): string
    {
        return "$this->name";
    }

    /**
     * https://symfony.com/doc/current/validation.html
     * we use php version for validation!!!
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank());

//#BlockStart number=64 id=_19_0_3_40d01a2_1635864239167_130089_6058_#_2
//        to remove constraint use following code
//        unset($metadata->properties['PROPERTY']);
//        unset($metadata->members['PROPERTY']);
//#BlockEnd number=64

    }

    #[Ignore]
    public function getGeneratedFilterFields(): array
    {
        return [
            "_stream.id",
            "_stream.name",
            "_stream.description",
            "_stream.order",
            "_stream.externalId",
        ];
    }

    #[Ignore]
    public function getUploadFields(): array
    {
        return [

        ];
    }

    #[Ignore]
    public function getReadOnlyFields(): array
    {
        return [
        ];
    }

    #[Ignore]
    public function getParentClasses(): array
    {
        return [
            "practice",
        ];
    }

    #[Ignore]
    public static array $manyToManyProperties = [
    ];

    #[Ignore]
    public static array $childProperties = [
        "streamActivities" => "stream",
    ];

//#BlockStart number=65 id=_19_0_3_40d01a2_1635864239167_130089_6058_#_3

    /**
     * The toString method based on the private __toString autogenerated method
     * If necessary override
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Return the stream letter drived from order
     * @return string
     */
    public function getLetter(): string
    {
        return match ($this->order) {
            Stream::STREAMLETTER_A => "A",
            Stream::STREAMLETTER_B => "B",
            default => "NOT SPECIFIED",
        };
    }

    public function getNameKey(): string
    {
        return $this->getPractice()->getNameKey().'-'.$this->getLetter();
    }

    /**
     * Convert from letter to order
     * @param $letter string
     * @return ?int
     */
    public static function getOrderByLetter($letter): ?int
    {
        return match ($letter) {
            "A" => Stream::STREAMLETTER_A,
            "B" => Stream::STREAMLETTER_B,
            default => null,
        };
    }

    /**
     * Get name
     * @return string|null
     */
    public function getTrimmedName(): ?string
    {
        $trimmedName = $this->name;
        if (strlen($trimmedName) > 40) {
            $trimmedName = rtrim(substr($trimmedName, 0, strpos($trimmedName, '/')));
        }

        return $trimmedName;
    }
//#BlockEnd number=65
}
