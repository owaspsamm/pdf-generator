<?php

declare(strict_types=1);

namespace App\Service\Processing;

use App\Entity\Abstraction\AbstractEntity;
use App\Entity\Activity;
use App\Entity\Answer;
use App\Entity\AnswerSet;
use App\Entity\BusinessFunction;
use App\Entity\MaturityLevel;
use App\Entity\Practice;
use App\Entity\PracticeLevel;
use App\Entity\Question;
use App\Entity\Stream;
use App\Repository\Abstraction\AbstractRepository;
use App\Repository\ActivityRepository;
use App\Repository\AnswerSetRepository;
use App\Repository\AnswerRepository;
use App\Repository\BusinessFunctionRepository;
use App\Repository\MaturityLevelRepository;
use App\Repository\PracticeLevelRepository;
use App\Repository\PracticeRepository;
use App\Repository\QuestionRepository;
use App\Repository\StreamRepository;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;

class YamlModelsToDbRecordsSyncer
{

    private ?string $rootFolder = null;
    private ?string $modelFolder = null;
    private bool $skipDelete = false;

    /**
     * YamlModelsToDbRecordsSyncer constructor.
     * @param ParameterBagInterface      $parameterBag
     * @param BusinessFunctionRepository $businessFunctionRepository
     * @param PracticeRepository         $practiceRepository
     * @param MaturityLevelRepository    $maturityLevelRepository
     * @param PracticeLevelRepository    $practiceLevelRepository
     * @param StreamRepository           $streamRepository
     * @param ActivityRepository         $activityRepository
     * @param QuestionRepository         $questionRepository
     * @param EntityManagerInterface     $entityManager
     * @param AnswerSetRepository        $answerSetRepository
     * @param AnswerRepository           $answerRepository
     */
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private BusinessFunctionRepository $businessFunctionRepository,
        private PracticeRepository $practiceRepository,
        private MaturityLevelRepository $maturityLevelRepository,
        private PracticeLevelRepository $practiceLevelRepository,
        private StreamRepository $streamRepository,
        private ActivityRepository $activityRepository,
        private QuestionRepository $questionRepository,
        private EntityManagerInterface $entityManager,
        private AnswerSetRepository $answerSetRepository,
        private AnswerRepository $answerRepository,
    )
    {
    }


    private function safeScandir(string $dir)
    {
        return is_dir($dir) ? scandir($dir) : [];
    }

    /**
     * @return int[]
     */
    public function syncBusinessFunctions(): array
    {
        $businessFunctionsFolderPath = "{$this->getModelsFolder()}/business_functions";
        $businessFunctionFiles = $this->removeDotDirectories($this->safeScandir($businessFunctionsFolderPath));
        $externalIds = [];
        $added = $modified = 0;
        foreach ($businessFunctionFiles as $businessFunctionYaml) {
            $isModified = false;
            $parsedYamlFile = Yaml::parseFile($businessFunctionsFolderPath."/".$businessFunctionYaml);
            $externalId = $parsedYamlFile["id"];

            /** @var BusinessFunction $businessFunctionEntity */
            $businessFunctionEntity = $this->createEntityIfNotExist(BusinessFunction::class, $externalId, $this->businessFunctionRepository);

            $oldName = $businessFunctionEntity->getName();
            $newName = $this->replaceCharacters((string)$parsedYamlFile["name"]);
            if ($this->arePropertiesDifferent($oldName, $newName)) {
                $businessFunctionEntity->setName($newName);
                $isModified = true;
            }

            $oldDescription = $businessFunctionEntity->getDescription();
            $newDescription = $this->replaceCharacters((string)$parsedYamlFile["description"]);
            if ($this->arePropertiesDifferent($oldDescription, $newDescription)) {
                $businessFunctionEntity->setDescription($newDescription);
                $isModified = true;
            }

            $oldOrder = $businessFunctionEntity->getOrder();
            $newOrder = $parsedYamlFile["order"];
            if ($this->arePropertiesDifferent($oldOrder, $newOrder)) {
                $businessFunctionEntity->setOrder($newOrder);
                $isModified = true;
            }

            [$added, $modified] = $this->getIncreasedCounter($added, $modified, $isModified, $businessFunctionEntity);
            $this->entityManager->persist($businessFunctionEntity);
            $externalIds[] = $externalId;
        }

        $deleted = $this->deleteEntities($this->businessFunctionRepository->findByExternalIdsNotIn($externalIds));
        $this->entityManager->flush();

        return [$added, $modified, $deleted];
    }

    /**
     * @return int[]
     */
    public function syncSecurityPractices(): array
    {
        $securityPracticesFolderPath = "{$this->getModelsFolder()}/security_practices";
        $securityPracticesFiles = $this->removeDotDirectories($this->safeScandir($securityPracticesFolderPath));
        $externalIds = [];
        $added = $modified = 0;
        foreach ($securityPracticesFiles as $securityPracticeYaml) {
            $isModified = false;
            $parsedYamlFile = Yaml::parseFile($securityPracticesFolderPath."/".$securityPracticeYaml);
            $externalId = $parsedYamlFile["id"];

            /** @var Practice $practiceEntity */
            $practiceEntity = $this->createEntityIfNotExist(Practice::class, $externalId, $this->practiceRepository);

            $oldBusinessFunctionId = $practiceEntity->getBusinessFunction()?->getExternalId() ?? "";
            $newBusinessFunctionId = $parsedYamlFile["function"];
            if ($this->arePropertiesDifferent($oldBusinessFunctionId, $newBusinessFunctionId)) {
                $oldBusinessFunctionEntity = $practiceEntity->getBusinessFunction();
                $practiceEntity->setBusinessFunction($this->businessFunctionRepository->findOneBy(["externalId" => $newBusinessFunctionId]));
                $isModified = $oldBusinessFunctionEntity !== $practiceEntity->getBusinessFunction();
            }

            $oldName = $practiceEntity->getName();
            $newName = $this->replaceCharacters((string)$parsedYamlFile["name"]);
            if ($this->arePropertiesDifferent($oldName, $newName)) {
                $practiceEntity->setName($newName);
                $isModified = true;
            }

            $oldShortName = $practiceEntity->getShortName();
            $newShortName = $this->replaceCharacters((string)$parsedYamlFile["shortName"]);
            if ($this->arePropertiesDifferent($oldShortName, $newShortName)) {
                $practiceEntity->setShortName($newShortName);
                $isModified = true;
            }

            $oldShortDesc = $practiceEntity->getShortDescription();
            $newShortDesc = $this->replaceCharacters((string)$parsedYamlFile["shortDescription"]);
            if ($this->arePropertiesDifferent($oldShortDesc, $newShortDesc)) {
                $practiceEntity->setShortDescription($newShortDesc);
                $isModified = true;
            }

            $oldLongDesc = $practiceEntity->getLongDescription();
            $newLongDesc = $this->replaceCharacters((string)$parsedYamlFile["longDescription"]);
            if ($this->arePropertiesDifferent($oldLongDesc, $newLongDesc)) {
                $practiceEntity->setLongDescription($newLongDesc);
                $isModified = true;
            }

            $oldOrder = $practiceEntity->getOrder();
            $newOrder = $parsedYamlFile["order"];
            if ($this->arePropertiesDifferent($oldOrder, $newOrder)) {
                $practiceEntity->setOrder($newOrder);
                $isModified = true;
            }

            [$added, $modified] = $this->getIncreasedCounter($added, $modified, $isModified, $practiceEntity);
            $this->entityManager->persist($practiceEntity);
            $externalIds[] = $externalId;
        }

        $deleted = $this->deleteEntities($this->practiceRepository->findByExternalIdsNotIn($externalIds));
        $this->entityManager->flush();

        return [$added, $modified, $deleted];
    }

    /**
     * @return int[]
     */
    public function syncMaturityLevels(): array
    {
        $maturityLevelsFolderPath = "{$this->getModelsFolder()}/maturity_levels";
        $maturityLevelFiles = $this->removeDotDirectories($this->safeScandir($maturityLevelsFolderPath));
        $externalIds = [];
        $added = $modified = 0;
        foreach ($maturityLevelFiles as $maturityLevelFile) {
            $isModified = false;
            $parsedYamlFile = Yaml::parseFile($maturityLevelsFolderPath."/".$maturityLevelFile);
            $externalId = $parsedYamlFile["id"];

            /** @var MaturityLevel $maturityLevelEntity */
            $maturityLevelEntity = $this->createEntityIfNotExist(MaturityLevel::class, $externalId, $this->maturityLevelRepository);

            $oldLevel = $maturityLevelEntity->getLevel();
            $newLevel = $parsedYamlFile["number"];
            if ($this->arePropertiesDifferent($oldLevel, $newLevel)) {
                $maturityLevelEntity->setLevel($newLevel);
                $isModified = true;
            }

            $oldDescription = $maturityLevelEntity->getDescription();
            $newDescription = $this->replaceCharacters((string)$parsedYamlFile["description"]);
            if ($this->arePropertiesDifferent($oldDescription, $newDescription)) {
                $maturityLevelEntity->setDescription($newDescription);
                $isModified = true;
            }

            [$added, $modified] = $this->getIncreasedCounter($added, $modified, $isModified, $maturityLevelEntity);
            $this->entityManager->persist($maturityLevelEntity);
            $externalIds[] = $externalId;
        }
        $deleted = $this->deleteEntities($this->maturityLevelRepository->findByExternalIdsNotIn($externalIds));
        $this->entityManager->flush();

        return [$added, $modified, $deleted];
    }

    /**
     * @return int[]
     */
    public function syncPracticeLevels(): array
    {
        $practiceLevelsFolderPath = "{$this->getModelsFolder()}/practice_levels";
        $practiceLevelFiles = $this->removeDotDirectories($this->safeScandir($practiceLevelsFolderPath));
        $externalIds = [];
        $added = $modified = 0;
        foreach ($practiceLevelFiles as $practiceLevel) {
            $isModified = false;
            $parsedYamlFile = Yaml::parseFile($practiceLevelsFolderPath."/".$practiceLevel);
            $externalId = $parsedYamlFile["id"];

            /** @var PracticeLevel $practiceLevelEntity */
            $practiceLevelEntity = $this->createEntityIfNotExist(PracticeLevel::class, $externalId, $this->practiceLevelRepository);

            $oldPracticeId = $practiceLevelEntity->getPractice()?->getExternalId() ?? "";
            $newPracticeId = $parsedYamlFile["practice"];
            if ($this->arePropertiesDifferent($oldPracticeId, $newPracticeId)) {
                $oldPractice = $practiceLevelEntity->getPractice();
                $practiceLevelEntity->setPractice($this->practiceRepository->findOneBy(["externalId" => $newPracticeId]));
                $isModified = $oldPractice !== $practiceLevelEntity->getPractice();
            }

            $oldMaturityLevelId = $practiceLevelEntity->getMaturityLevel()?->getExternalId() ?? "";
            $newMaturityLevelId = $parsedYamlFile["maturitylevel"];
            if ($this->arePropertiesDifferent($oldMaturityLevelId, $newMaturityLevelId)) {
                $oldMaturityLevelEntity = $practiceLevelEntity->getMaturityLevel();
                $practiceLevelEntity->setMaturityLevel($this->maturityLevelRepository->findOneBy(["externalId" => $newMaturityLevelId]));
                $isModified = $oldMaturityLevelEntity !== $practiceLevelEntity->getMaturityLevel();
            }

            $oldObjective = $practiceLevelEntity->getObjective();
            $newObjective = $this->replaceCharacters((string)$parsedYamlFile["objective"]);
            if ($this->arePropertiesDifferent($oldObjective, $newObjective)) {
                $practiceLevelEntity->setObjective($newObjective);
                $isModified = true;
            }

            [$added, $modified] = $this->getIncreasedCounter($added, $modified, $isModified, $practiceLevelEntity);
            $this->entityManager->persist($practiceLevelEntity);
            $externalIds[] = $externalId;
        }

        $deleted = $this->deleteEntities($this->practiceLevelRepository->findByExternalIdsNotIn($externalIds));
        $this->entityManager->flush();

        return [$added, $modified, $deleted];
    }

    /**
     * @return int[]
     */
    public function syncStreams(): array
    {
        $streamsFolderPath = "{$this->getModelsFolder()}/streams";
        $streamFiles = $this->removeDotDirectories($this->safeScandir($streamsFolderPath));
        $externalIds = [];
        $added = $modified = 0;
        foreach ($streamFiles as $streamFile) {
            $isModified = false;
            $parsedYamlFile = Yaml::parseFile($streamsFolderPath."/".$streamFile);
            $externalId = $parsedYamlFile["id"];

            /** @var Stream $streamEntity */
            $streamEntity = $this->createEntityIfNotExist(Stream::class, $externalId, $this->streamRepository);

            $oldPracticeId = $streamEntity->getPractice()?->getExternalId() ?? "";
            $newPracticeId = $parsedYamlFile["practice"];
            if ($this->arePropertiesDifferent($oldPracticeId, $newPracticeId)) {
                $oldPracticeEntity = $streamEntity->getPractice();
                $streamEntity->setPractice($this->practiceRepository->findOneBy(["externalId" => $newPracticeId]));
                $isModified = $oldPracticeEntity !== $streamEntity->getPractice();
            }

            $oldName = $streamEntity->getName();
            $newName = $this->replaceCharacters((string)$parsedYamlFile["name"]);
            if ($this->arePropertiesDifferent($oldName, $newName)) {
                $streamEntity->setName($newName);
                $isModified = true;
            }

            $oldDescription = $streamEntity->getDescription();
            $newDescription = $this->replaceCharacters((string)$parsedYamlFile["description"]);
            if ($this->arePropertiesDifferent($oldDescription, $newDescription)) {
                $streamEntity->setDescription($newDescription);
                $isModified = true;
            }

            $oldOrder = $streamEntity->getOrder();
            $newOrder = $parsedYamlFile["order"];
            if ($this->arePropertiesDifferent($oldOrder, $newOrder)) {
                $streamEntity->setOrder($newOrder);
                $isModified = true;
            }

            [$added, $modified] = $this->getIncreasedCounter($added, $modified, $isModified, $streamEntity);
            $this->entityManager->persist($streamEntity);
            $externalIds[] = $externalId;
        }

        $deleted = $this->deleteEntities($this->streamRepository->findByExternalIdsNotIn($externalIds));
        $this->entityManager->flush();

        return [$added, $modified, $deleted];
    }

    /**
     * @return int[]
     */
    public function syncActivities(): array
    {
        $activitiesFolderPath = "{$this->getModelsFolder()}/activities";
        $activityFiles = $this->removeDotDirectories($this->safeScandir($activitiesFolderPath));
        $externalIds = [];
        $added = $modified = 0;
        foreach ($activityFiles as $activityFile) {
            $isModified = false;
            $parsedYamlFile = Yaml::parseFile($activitiesFolderPath."/".$activityFile);
            $externalId = $parsedYamlFile["id"];

            /** @var Activity $activityEntity */
            $activityEntity = $this->createEntityIfNotExist(Activity::class, $externalId, $this->activityRepository);

            $oldStreamId = $activityEntity->getStream()?->getExternalId() ?? "";
            $newStreamId = $parsedYamlFile["stream"];
            if ($this->arePropertiesDifferent($oldStreamId, $newStreamId)) {
                $oldStreamEntity = $activityEntity->getStream();
                $activityEntity->setStream($this->streamRepository->findOneBy(["externalId" => $newStreamId]));
                $isModified = $oldStreamEntity !== $activityEntity->getStream();
            }

            $oldPracticeLevelId = $activityEntity->getPracticeLevel()?->getExternalId() ?? "";
            $newPracticeLevelId = $parsedYamlFile["level"];
            if ($this->arePropertiesDifferent($oldPracticeLevelId, $newPracticeLevelId)) {
                $oldPracticeLevelEntity = $activityEntity->getPracticeLevel();
                $activityEntity->setPracticeLevel($this->practiceLevelRepository->findOneBy(["externalId" => $newPracticeLevelId]));
                $isModified = $oldPracticeLevelEntity !== $activityEntity->getPracticeLevel();
            }

            $oldTitle = $activityEntity->getTitle();
            $newTitle = $this->replaceCharacters((string)$parsedYamlFile["title"]);
            if ($this->arePropertiesDifferent($oldTitle, $newTitle)) {
                $activityEntity->setTitle($newTitle);
                $isModified = true;
            }

            $oldBenefit = $activityEntity->getBenefit();
            $newBenefit = $this->replaceCharacters((string)$parsedYamlFile["benefit"]);
            if ($this->arePropertiesDifferent($oldBenefit, $newBenefit)) {
                $activityEntity->setBenefit($newBenefit);
                $isModified = true;
            }

            $oldShortDesc = $activityEntity->getShortDescription();
            $newShortDesc = $this->replaceCharacters((string)$parsedYamlFile["shortDescription"]);
            if ($this->arePropertiesDifferent($oldShortDesc, $newShortDesc)) {
                $activityEntity->setShortDescription($newShortDesc);
                $isModified = true;
            }

            $oldLongDesc = $activityEntity->getLongDescription();
            $newLongDesc = $this->replaceCharacters((string)$parsedYamlFile["longDescription"]);
            if ($this->arePropertiesDifferent($oldLongDesc, $newLongDesc)) {
                $activityEntity->setLongDescription($newLongDesc);
                $isModified = true;
            }

            $oldNotes = $activityEntity->getNotes();
            $newNotes = $this->replaceCharacters((string)$parsedYamlFile["notes"]);
            if ($this->arePropertiesDifferent($oldNotes, $newNotes)) {
                $activityEntity->setNotes($newNotes);
                $isModified = true;
            }

            [$added, $modified] = $this->getIncreasedCounter($added, $modified, $isModified, $activityEntity);
            $this->entityManager->persist($activityEntity);
            $externalIds[] = $externalId;
        }

        $deleted = $this->deleteEntities($this->activityRepository->findByExternalIdsNotIn($externalIds));
        $this->entityManager->flush();

        return [$added, $modified, $deleted];
    }

    /**
     * @return int[]
     */
    public function syncQuestions(): array
    {
        $questionsFolderPath = "{$this->getModelsFolder()}/questions";
        $questionFiles = $this->removeDotDirectories($this->safeScandir($questionsFolderPath));
        $externalIds = [];
        $added = $modified = 0;
        foreach ($questionFiles as $questionFile) {
            $isModified = false;
            $parsedYamlFile = Yaml::parseFile($questionsFolderPath."/".$questionFile);
            $externalId = $parsedYamlFile["id"];

            /** @var Question $questionEntity */
            $questionEntity = $this->createEntityIfNotExist(Question::class, $externalId, $this->questionRepository);

            $oldActivityId = $questionEntity->getActivity()?->getExternalId() ?? "";
            $newActivityId = $parsedYamlFile["activity"];
            if ($this->arePropertiesDifferent($oldActivityId, $newActivityId)) {
                $oldActivityEntity = $questionEntity->getActivity();
                $questionEntity->setActivity($this->activityRepository->findOneBy(["externalId" => $newActivityId]));
                $isModified = $oldActivityEntity !== $questionEntity->getActivity();
            }

            $oldAnswerSetId = $questionEntity->getAnswerSet()?->getExternalId() ?? "";
            $newAnswerSetId = $parsedYamlFile["answerset"];
            if ($this->arePropertiesDifferent($oldAnswerSetId, $newAnswerSetId)) {
                $oldAnswerSetEntity = $questionEntity->getAnswerSet();
                $questionEntity->setAnswerSet($this->answerSetRepository->findOneBy(["externalId" => $newAnswerSetId]));
                $isModified = $oldAnswerSetEntity !== $questionEntity->getAnswerSet();
            }

            $oldText = $questionEntity->getText();
            $newText = $this->replaceCharacters((string)$parsedYamlFile["text"]);
            if ($this->arePropertiesDifferent($oldText, $newText)) {
                $questionEntity->setText($newText);
                $isModified = true;
            }

            $oldOrder = $questionEntity->getOrder();
            $newOrder = $parsedYamlFile["order"];
            if ($this->arePropertiesDifferent($oldOrder, $newOrder)) {
                $questionEntity->setOrder($newOrder);
                $isModified = true;
            }

            $oldQuality = $questionEntity->getQuality();
            $newQuality = $this->replaceCharacters(implode("\n", $parsedYamlFile["quality"]));
            if ($this->arePropertiesDifferent($oldQuality, $newQuality)) {
                $questionEntity->setQuality($newQuality);
                $isModified = true;
            }

            [$added, $modified] = $this->getIncreasedCounter($added, $modified, $isModified, $questionEntity);
            $this->entityManager->persist($questionEntity);
            $externalIds[] = $externalId;
        }

        $deleted = $this->deleteEntities($this->questionRepository->findByExternalIdsNotIn($externalIds));
        $this->entityManager->flush();

        return [$added, $modified, $deleted];
    }

    /**
     * @return int[]
     */
    public function syncAnswerSets(): array
    {
        $answersFolderPath = "{$this->getModelsFolder()}/answer_sets";
        $answerFiles = $this->removeDotDirectories($this->safeScandir($answersFolderPath));
        $externalIds = [];
        $added = $modified = 0;
        foreach ($answerFiles as $answerFile) {
            $isModified = false;
            $parsedYamlFile = Yaml::parseFile($answersFolderPath."/".$answerFile);
            $externalId = $parsedYamlFile["id"];

            /** @var AnswerSet $answerSetEntity */
            $answerSetEntity = $this->createEntityIfNotExist(AnswerSet::class, $externalId, $this->answerSetRepository);
            $answerEntities = [];
            foreach ($parsedYamlFile["values"] as $answerYamlFileValues) {
                $order = $answerYamlFileValues["order"];
                /** @var Answer $answerEntity */
                $answerEntity = $this->createAnswerIfNotExist($answerSetEntity, $order, $this->answerRepository);

                $answerEntity->setAnswerSet($answerSetEntity);

                $oldOrder = $answerEntity->getOrder();
                $newOrder = $this->replaceCharacters((string)$answerYamlFileValues["order"]);
                if ($this->arePropertiesDifferent($oldOrder, $newOrder)) {
                    $answerEntity->setOrder((int)$newOrder);
                    $isModified = true;
                }

                $oldText = $answerEntity->getText();
                $newText = $this->replaceCharacters((string)$answerYamlFileValues["text"]);
                if ($this->arePropertiesDifferent($oldText, $newText)) {
                    $answerEntity->setText($newText);
                    $isModified = true;
                }

                $oldValue = $answerEntity->getValue();
                $newValue = $answerYamlFileValues["value"];
                if ($this->arePropertiesDifferent($oldValue, $newValue)) {
                    $answerEntity->setValue($newValue);
                    $isModified = true;
                }

                $oldWeight = $answerEntity->getWeight();
                $newWeight = $answerYamlFileValues["weight"];
                if ($this->arePropertiesDifferent($oldWeight, $newWeight)) {
                    $answerEntity->setWeight($newWeight);
                    $isModified = true;
                }

                $answerEntities[] = $answerEntity;
            }
            [$added, $modified] = $this->getIncreasedCounter($added, $modified, $isModified, $answerSetEntity);
            foreach ($answerEntities as $answerEntity) {
                $this->entityManager->persist($answerEntity);
            }
            $this->entityManager->persist($answerSetEntity);
            $externalIds[] = $externalId;
        }

        $deleted = $this->deleteEntities($this->answerSetRepository->findByExternalIdsNotIn($externalIds));
        $this->entityManager->flush();

        return [$added, $modified, $deleted];
    }

    /**
     * @param string             $className
     * @param string             $id
     * @param AbstractRepository $repository
     * @return AbstractEntity
     */
    private function createEntityIfNotExist(string $className, string $id, AbstractRepository $repository): AbstractEntity
    {
        $entity = $repository->findOneBy(["externalId" => $id]);
        if ($entity === null) {
            $entity = new $className();
            $entity->setExternalId($id);
        }

        return $entity;
    }

    /**
     * @param AnswerSet        $answerSet
     * @param int              $order
     * @param AnswerRepository $repository
     * @param string           $className
     * @return AbstractEntity
     */
    private function createAnswerIfNotExist(AnswerSet $answerSet, int $order, AnswerRepository $repository, string $className = Answer::class): AbstractEntity
    {
        $entity = $repository->findOneBy(["answerSet" => $answerSet, "order" => $order]);
        /** @var Answer|null $entity */
        if ($entity === null) {
            $entity = new $className();
            $entity->setOrder($order);
        }

        return $entity;
    }

    /**
     * @param array $entitiesForRemoval
     * @return int
     */
    private function deleteEntities(array $entitiesForRemoval): int
    {
        if ($this->skipDelete) {
            return 0;
        }
        $deleted = 0;
        /** @var AbstractEntity $entity */
        foreach ($entitiesForRemoval as $entity) {
            $this->entityManager->remove($entity);
            $deleted++;
        }

        return $deleted;
    }

    /**
     * @param string|null $text
     * @return string|null
     */
    private function replaceCharacters(?string $text): string|null
    {
        return $text === null ? $text : str_replace("&", "and", $text);
    }

    /**
     * @param int            $added
     * @param int            $modified
     * @param bool           $isModified
     * @param AbstractEntity $entity
     * @return int[]
     */
    #[Pure]
    private function getIncreasedCounter(int $added, int $modified, bool $isModified, AbstractEntity $entity): array
    {
        if ($entity->getCreatedAt() === null) {
            $added++;
        } elseif ($isModified) {
            $modified++;
        }
        return [$added, $modified];
    }

    /**
     * @param mixed $oldProp
     * @param mixed $newProp
     * @return bool
     */
    private function arePropertiesDifferent(mixed $oldProp, mixed $newProp): bool
    {
        return (string)$oldProp !== (string)$newProp;
    }

    /**
     * @return string
     */
    private function getModelsFolder(): string
    {
        return "{$this->parameterBag->get('kernel.project_dir')}/private/".$this->rootFolder.$this->modelFolder;
    }

    /**
     * @param array $directories
     * @return array
     */
    private function removeDotDirectories(array $directories): array
    {
        return array_filter($directories, fn($dir) => $dir !== "." && $dir !== "..");
    }

    /**
     * @param string|null $rootFolder
     */
    public function setRootFolder(?string $rootFolder): void
    {
        $this->rootFolder = $rootFolder;
    }

    /**
     * @param string|null $modelFolder
     */
    public function setModelFolder(?string $modelFolder): void
    {
        $this->modelFolder = "/".$modelFolder;
    }

    /**
     * @param bool $skipDelete
     */
    public function setSkipDelete(bool $skipDelete): void
    {
        $this->skipDelete = $skipDelete;
    }
}
