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

//#BlockStart number=53 id=_19_0_3_40d01a2_1635864210817_220463_6011_#_0

//#BlockEnd number=53


#[ORM\Table(name: "`practice`")]
#[ORM\Entity(repositoryClass: "App\Repository\PracticeRepository")]
#[ORM\HasLifecycleCallbacks]
class Practice extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: BusinessFunction::class, cascade: ["persist"], fetch: "EAGER", inversedBy: "businessFunctionPractices")]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    #[MaxDepth(1)]
    protected ?BusinessFunction $businessFunction = null;

    #[ORM\Column(name: "`name`", type: Types::STRING, nullable: true)]
    protected ?string $name = "";

    #[ORM\Column(name: "`short_name`", type: Types::STRING, nullable: true)]
    protected ?string $shortName = "";

    #[ORM\Column(name: "`short_description`", type: Types::TEXT, nullable: true)]
    protected ?string $shortDescription = "";

    #[ORM\Column(name: "`long_description`", type: Types::TEXT, nullable: true)]
    protected ?string $longDescription = "";

    #[ORM\Column(name: "`order`", type: Types::INTEGER)]
    protected int $order = 0;

    #[ORM\Column(name: "`external_id`", type: Types::STRING, nullable: true)]
    protected ?string $externalId = "";

    #[ORM\Column(name: "`icon`", type: Types::STRING, nullable: true)]
    protected ?string $icon = "";


    #[ORM\OneToMany(mappedBy: "practice", targetEntity: Stream::class, cascade: ["persist"], fetch: "LAZY", orphanRemoval: false)]
    #[ORM\OrderBy(["order" => "ASC"])]
    #[MaxDepth(1)]
    protected Collection $practiceStreams;

    #[ORM\OneToMany(mappedBy: "practice", targetEntity: PracticeLevel::class, cascade: ["persist"], fetch: "LAZY", orphanRemoval: false)]
    #[ORM\OrderBy(["id" => "ASC"])]
    #[MaxDepth(1)]
    protected Collection $practicePracticeLevels;



    /**
     * Practice constructor
     * @return void
     */
    public function __construct()
    {
        $this->practiceStreams = new ArrayCollection();
        $this->practicePracticeLevels = new ArrayCollection();
    }

    /**
     * Set businessFunction
     * @param BusinessFunction|null $businessFunction the setter value
     * @return Practice
     */
    public function setBusinessFunction(?BusinessFunction $businessFunction): self
    {
        $this->businessFunction = $businessFunction;

        return $this;
    }


    /**
     * Get businessFunction
     * @return BusinessFunction|null
     */
    public function getBusinessFunction(): ?BusinessFunction
    {
        return $this->businessFunction;
    }

    /**
     * Set name
     * @param string|null $name the setter value
     * @return Practice
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
     * Set shortName
     * @param string|null $shortName the setter value
     * @return Practice
     */
    public function setShortName(?string $shortName): self
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     * @return string|null
     */
    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    /**
     * Set shortDescription
     * @param string|null $shortDescription the setter value
     * @return Practice
     */
    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     * @return string|null
     */
    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    /**
     * Set longDescription
     * @param string|null $longDescription the setter value
     * @return Practice
     */
    public function setLongDescription(?string $longDescription): self
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    /**
     * Get longDescription
     * @return string|null
     */
    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    /**
     * Set order
     * @param int $order the setter value
     * @return Practice
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
     * @return Practice
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
     * Set icon
     * @param string|null $icon the setter value
     * @return Practice
     */
    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Get Practice Streams
     * @return Collection<Stream>
     */
    public function getPracticeStreams(): Collection
    {
        return $this->practiceStreams;
    }

    /**
     * Add Streams Stream
     * @param Stream $stream
     * @return Practice
     */
    public function addPracticeStream(Stream $stream): Practice
    {
        $this->practiceStreams->add($stream);

        return $this;
    }

    /**
     * Get Practice PracticeLevels
     * @return Collection<PracticeLevel>
     */
    public function getPracticePracticeLevels(): Collection
    {
        return $this->practicePracticeLevels;
    }

    /**
     * Add PracticeLevels PracticeLevel
     * @param PracticeLevel $practiceLevel
     * @return Practice
     */
    public function addPracticePracticeLevel(PracticeLevel $practiceLevel): Practice
    {
        $this->practicePracticeLevels->add($practiceLevel);

        return $this;
    }


    /**
     * This method is a copy constructor that will return a copy object (except for the id field)
     * Note that this method will not save the object
     * @param Practice|null $clone a clone object that is either null or already partially initialized
     * @return Practice
     */
    #[Ignore]
    public function getCopy(?Practice $clone = null): Practice
    {
        if ($clone == null) {
            $clone = new Practice();
        }
        $clone->setBusinessFunction($this->businessFunction);
        $clone->setName($this->name);
        $clone->setShortName($this->shortName);
        $clone->setShortDescription($this->shortDescription);
        $clone->setLongDescription($this->longDescription);
        $clone->setOrder($this->order);
        $clone->setExternalId($this->externalId);
        $clone->setIcon($this->icon);
//#BlockStart number=54 id=_19_0_3_40d01a2_1635864210817_220463_6011_#_1

//#BlockEnd number=54

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

//#BlockStart number=55 id=_19_0_3_40d01a2_1635864210817_220463_6011_#_2
//        to remove constraint use following code
//        unset($metadata->properties['PROPERTY']);
//        unset($metadata->members['PROPERTY']);
//#BlockEnd number=55
    }

    #[Ignore]
    public function getGeneratedFilterFields(): array
    {
        return [
            "_practice.id",
            "_practice.name",
            "_practice.shortName",
            "_practice.shortDescription",
            "_practice.longDescription",
            "_practice.order",
            "_practice.externalId",
            "_practice.icon",
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
             "businessFunction",
        ];
    }

    #[Ignore]
    public static array $manyToManyProperties = [
    ];

    #[Ignore]
    public static array $childProperties = [
        "practiceStreams" => "practice",
        "practicePracticeLevels" => "practice",
    ];

//#BlockStart number=56 id=_19_0_3_40d01a2_1635864210817_220463_6011_#_3

    /**
     * The toString method based on the private __toString autogenerated method
     * If necessary override
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    public function getNameKey(): string
    {
        return $this->getBusinessFunction()->getNameKey() . '-' . $this->getShortName();
    }

    /**
     * Returns the practice stream associated with the Practice by its letter
     * @param $letter string
     * @return Stream|null
     */
    public function getPracticeStreamByLetter($letter): ?Stream
    {
        $letter = Stream::getOrderByLetter($letter);

        return $this->practiceStreams->filter(function ($stream) use ($letter) {
            return $stream->getOrder() === $letter;
        })->first();
    }
//#BlockEnd number=56
}
