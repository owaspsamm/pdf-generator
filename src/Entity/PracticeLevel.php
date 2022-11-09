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



//#BlockStart number=80 id=_19_0_3_40d01a2_1635864815057_862891_6258_#_0

//#BlockEnd number=80


#[ORM\Table(name: "`practice_level`")]
#[ORM\Entity(repositoryClass: "App\Repository\PracticeLevelRepository")]
#[ORM\HasLifecycleCallbacks]
class PracticeLevel extends AbstractEntity
{

    #[ORM\ManyToOne(targetEntity: MaturityLevel::class, cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    #[MaxDepth(1)]
    protected ?MaturityLevel $maturityLevel = null;

    #[ORM\ManyToOne(targetEntity: Practice::class, cascade: ["persist"], fetch: "EAGER", inversedBy: "practicePracticeLevels")]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    #[MaxDepth(1)]
    protected ?Practice $practice = null;

    #[ORM\Column(name: "`objective`", type: Types::STRING, nullable: true)]
    protected ?string $objective = "";

    #[ORM\Column(name: "`external_id`", type: Types::STRING, nullable: true)]
    protected ?string $externalId = "";



    /**
     * PracticeLevel constructor
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Set maturityLevel
     * @param MaturityLevel|null $maturityLevel the setter value
     * @return PracticeLevel
     */
    public function setMaturityLevel(?MaturityLevel $maturityLevel): self
    {
        $this->maturityLevel = $maturityLevel;

        return $this;
    }

    /**
     * Get maturityLevel
     * @return MaturityLevel|null
     */
    public function getMaturityLevel(): ?MaturityLevel
    {
        return $this->maturityLevel;
    }

    /**
     * Set practice
     * @param Practice|null $practice the setter value
     * @return PracticeLevel
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
     * Set objective
     * @param string|null $objective the setter value
     * @return PracticeLevel
     */
    public function setObjective(?string $objective): self
    {
        $this->objective = $objective;

        return $this;
    }

    /**
     * Get objective
     * @return string|null
     */
    public function getObjective(): ?string
    {
        return $this->objective;
    }

    /**
     * Set externalId
     * @param string|null $externalId the setter value
     * @return PracticeLevel
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
     * This method is a copy constructor that will return a copy object (except for the id field)
     * Note that this method will not save the object
     * @param PracticeLevel|null $clone a clone object that is either null or already partially initialized
     * @return PracticeLevel
     */
    #[Ignore]
    public function getCopy(?PracticeLevel $clone = null): PracticeLevel
    {
        if ($clone == null) {
            $clone = new PracticeLevel();
        }
        $clone->setMaturityLevel($this->maturityLevel);
        $clone->setPractice($this->practice);
        $clone->setObjective($this->objective);
        $clone->setExternalId($this->externalId);
//#BlockStart number=81 id=_19_0_3_40d01a2_1635864815057_862891_6258_#_1

//#BlockEnd number=81

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

//#BlockStart number=82 id=_19_0_3_40d01a2_1635864815057_862891_6258_#_2
//        to remove constraint use following code
//        unset($metadata->properties['PROPERTY']);
//        unset($metadata->members['PROPERTY']);
//#BlockEnd number=82

    }

    #[Ignore]
    public function getGeneratedFilterFields(): array
    {
		return [
            "_practice_level.id",
            "_practice_level.objective",
            "_practice_level.externalId",
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
    ];

//#BlockStart number=83 id=_19_0_3_40d01a2_1635864815057_862891_6258_#_3

    /**
     * The toString method based on the private __toString autogenerated method
     * If necessary override
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
    


//#BlockEnd number=83

}
