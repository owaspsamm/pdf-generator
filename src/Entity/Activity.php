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



//#BlockStart number=89 id=_19_0_3_40d01a2_1635864872516_976335_6324_#_0

//#BlockEnd number=89


#[ORM\Table(name: "`activity`")]
#[ORM\Entity(repositoryClass: "App\Repository\ActivityRepository")]
#[ORM\HasLifecycleCallbacks]
class Activity extends AbstractEntity
{

    #[ORM\ManyToOne(targetEntity: PracticeLevel::class, cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    #[MaxDepth(1)]
    protected ?PracticeLevel $practiceLevel = null;

    #[ORM\ManyToOne(targetEntity: Stream::class, cascade: ["persist"], fetch: "EAGER", inversedBy: "streamActivities")]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    #[MaxDepth(1)]
    protected ?Stream $stream = null;

    #[ORM\Column(name: "`title`", type: Types::STRING, nullable: true)]
    protected ?string $title = "";

    #[ORM\Column(name: "`benefit`", type: Types::STRING, nullable: true)]
    protected ?string $benefit = "";

    #[ORM\Column(name: "`short_description`", type: Types::TEXT, nullable: true)]
    protected ?string $shortDescription = "";

    #[ORM\Column(name: "`long_description`", type: Types::TEXT, nullable: true)]
    protected ?string $longDescription = "";

    #[ORM\Column(name: "`notes`", type: Types::TEXT, nullable: true)]
    protected ?string $notes = "";

    #[ORM\Column(name: "`external_id`", type: Types::STRING, nullable: true)]
    protected ?string $externalId = "";

    #[ORM\OneToMany(mappedBy: "activity", targetEntity: Question::class, cascade: ["persist"], fetch: "LAZY", orphanRemoval: false)]
    #[ORM\OrderBy(["order" => "ASC"])]
    #[MaxDepth(1)]
    protected Collection $activityQuestions;



    /**
     * Activity constructor
     * @return void
     */
    public function __construct()
    {
        $this->activityQuestions = new ArrayCollection();

    }

    /**
     * Set practiceLevel
     * @param PracticeLevel|null $practiceLevel the setter value
     * @return Activity
     */
    public function setPracticeLevel(?PracticeLevel $practiceLevel): self
    {
        $this->practiceLevel = $practiceLevel;

        return $this;
    }

    /**
     * Get practiceLevel
     * @return PracticeLevel|null
     */
    public function getPracticeLevel(): ?PracticeLevel
    {
        return $this->practiceLevel;
    }

    /**
     * Set stream
     * @param Stream|null $stream the setter value
     * @return Activity
     */
    public function setStream(?Stream $stream): self
    {
        $this->stream = $stream;

        return $this;
    }

    /**
     * Get stream
     * @return Stream|null
     */
    public function getStream(): ?Stream
    {
        return $this->stream;
    }

    /**
     * Set title
     * @param string|null $title the setter value
     * @return Activity
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set benefit
     * @param string|null $benefit the setter value
     * @return Activity
     */
    public function setBenefit(?string $benefit): self
    {
        $this->benefit = $benefit;

        return $this;
    }

    /**
     * Get benefit
     * @return string|null
     */
    public function getBenefit(): ?string
    {
        return $this->benefit;
    }

    /**
     * Set shortDescription
     * @param string|null $shortDescription the setter value
     * @return Activity
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
     * @return Activity
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
     * Set notes
     * @param string|null $notes the setter value
     * @return Activity
     */
    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     * @return string|null
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * Set externalId
     * @param string|null $externalId the setter value
     * @return Activity
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
     * Get Activity Questions
     * @return Collection<Question>
     */
    public function getActivityQuestions(): Collection
    {
        return $this->activityQuestions;
    }

    /**
     * Add Questions Question
     * @param Question $question
     * @return Activity
     */
    public function addActivityQuestion(Question $question): Activity
    {
        $this->activityQuestions->add($question);

        return $this;
    }


    /**
     * This method is a copy constructor that will return a copy object (except for the id field)
     * Note that this method will not save the object
     * @param Activity|null $clone a clone object that is either null or already partially initialized
     * @return Activity
     */
    #[Ignore]
    public function getCopy(?Activity $clone = null): Activity
    {
        if ($clone == null) {
            $clone = new Activity();
        }
        $clone->setPracticeLevel($this->practiceLevel);
        $clone->setStream($this->stream);
        $clone->setTitle($this->title);
        $clone->setBenefit($this->benefit);
        $clone->setShortDescription($this->shortDescription);
        $clone->setLongDescription($this->longDescription);
        $clone->setNotes($this->notes);
        $clone->setExternalId($this->externalId);
//#BlockStart number=90 id=_19_0_3_40d01a2_1635864872516_976335_6324_#_1

//#BlockEnd number=90

        return $clone;
    }

    /**
     * Private to string method auto generated based on the UML properties
     * This is the new way of doing things.
     * @return string
     */
    public function toString(): string
    {
        return "$this->id";
    }

    /**
     * https://symfony.com/doc/current/validation.html
     * we use php version for validation!!!
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {

//#BlockStart number=91 id=_19_0_3_40d01a2_1635864872516_976335_6324_#_2
//        to remove constraint use following code
//        unset($metadata->properties['PROPERTY']);
//        unset($metadata->members['PROPERTY']);
//#BlockEnd number=91

    }

    #[Ignore]
    public function getGeneratedFilterFields(): array
    {
		return [
            "_activity.id",
            "_activity.title",
            "_activity.benefit",
            "_activity.shortDescription",
            "_activity.longDescription",
            "_activity.notes",
            "_activity.externalId",
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
		     "stream",
        ];
    }

    #[Ignore]
    public static array $manyToManyProperties = [
    ];

    #[Ignore]
    public static array $childProperties = [
        "activityQuestions" => "activity",
    ];

//#BlockStart number=92 id=_19_0_3_40d01a2_1635864872516_976335_6324_#_3

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
        return $this->getStream()->getNameKey().'-'.$this->getPracticeLevel()->getMaturityLevel()->getLevel();
    }


//#BlockEnd number=92

}
