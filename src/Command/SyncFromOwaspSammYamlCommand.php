<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Activity;
use App\Entity\AnswerSet;
use App\Entity\BusinessFunction;
use App\Entity\MaturityLevel;
use App\Entity\Practice;
use App\Entity\PracticeLevel;
use App\Entity\Question;
use App\Entity\Stream;
use App\Service\Processing\YamlModelsToDbRecordsSyncer;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class SyncFromOwaspSammYamlCommand extends Command
{
    protected static $defaultName = 'app:sync-from-owasp-samm';
    private const ROOT_FOLDER_COMMAND_PARAMETER_NAME = "root";
    private const MODEL_FOLDER_COMMAND_PARAMETER_NAME = "model";
    private const SKIP_DELETE_COMMAND_PARAMETER_NAME = "skipDelete";


    /**
     * SyncFromOwaspSammYamlCommand constructor.
     * @param YamlModelsToDbRecordsSyncer $dbRecordsSyncer
     * @param EntityManagerInterface      $entityManager
     */
    public function __construct(private YamlModelsToDbRecordsSyncer $dbRecordsSyncer, private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument(self::ROOT_FOLDER_COMMAND_PARAMETER_NAME, InputArgument::OPTIONAL, 'root folder');
        $this->addArgument(self::MODEL_FOLDER_COMMAND_PARAMETER_NAME, InputArgument::OPTIONAL, 'model folder');
        $this->addArgument(self::SKIP_DELETE_COMMAND_PARAMETER_NAME, InputArgument::OPTIONAL, 'skip delete', false);
        $this->setDescription("Gets data from yaml files and adds/updates/removes records to the DB according to the files");
    }

    /**
     * The order in which the steps are invoked should be right, otherwise it may fail for foreign key constraints.
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws Throwable
     * @throws ConnectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rootFolder = $input->getArgument(self::ROOT_FOLDER_COMMAND_PARAMETER_NAME);
        if ($rootFolder) {
            $this->dbRecordsSyncer->setRootFolder($rootFolder);
        }
        $modelFolder = $input->getArgument(self::MODEL_FOLDER_COMMAND_PARAMETER_NAME);
        if ($modelFolder) {
            $this->dbRecordsSyncer->setModelFolder($modelFolder);
        }
        $skipDelete = $input->getArgument(self::SKIP_DELETE_COMMAND_PARAMETER_NAME);
        $this->dbRecordsSyncer->setSkipDelete((bool)$skipDelete);

        $this->entityManager->getConnection()->beginTransaction();
        try {
            // changing the order may fail because of FK constraints
            [$addedBusinessFuncs, $modifiedBusinessFuncs, $deletedBusinessFuncs] = $this->dbRecordsSyncer->syncBusinessFunctions();
            [$addedSecurityPractices, $modifiedSecurityPractices, $deletedSecurityPractices] = $this->dbRecordsSyncer->syncSecurityPractices();
            [$addedMaturityLevels, $modifiedMaturityLevels, $deletedMaturityLevels] = $this->dbRecordsSyncer->syncMaturityLevels();
            [$addedPracticeLevels, $modifiedPracticeLevels, $deletedPracticeLevels] = $this->dbRecordsSyncer->syncPracticeLevels();
            [$addedStreams, $modifiedStreams, $deletedStreams] = $this->dbRecordsSyncer->syncStreams();
            [$addedActivities, $modifiedActivities, $deletedActivities] = $this->dbRecordsSyncer->syncActivities();
            [$addedAnswerSets, $modifiedAnswerSets, $deletedAnswerSets] = $this->dbRecordsSyncer->syncAnswerSets();
            [$addedQuestions, $modifiedQuestions, $deletedQuestions] = $this->dbRecordsSyncer->syncQuestions();
            $this->entityManager->getConnection()->commit();
        } catch (Throwable $t) {
            $this->entityManager->getConnection()->rollBack();
            $this->entityManager->clear();
            $output->writeln("Failed");
            throw $t;
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Name', 'Added', 'Updated', 'Deleted'])
            ->setRows([
                [BusinessFunction::class, $addedBusinessFuncs, $modifiedBusinessFuncs, $deletedBusinessFuncs],
                [Practice::class, $addedSecurityPractices, $modifiedSecurityPractices, $deletedSecurityPractices],
                [MaturityLevel::class, $addedMaturityLevels, $modifiedMaturityLevels, $deletedMaturityLevels],
                [PracticeLevel::class, $addedPracticeLevels, $modifiedPracticeLevels, $deletedPracticeLevels],
                [Stream::class, $addedStreams, $modifiedStreams, $deletedStreams],
                [Activity::class, $addedActivities, $modifiedActivities, $deletedActivities],
                [AnswerSet::class, $addedAnswerSets, $modifiedAnswerSets, $deletedAnswerSets],
                [Question::class, $addedQuestions, $modifiedQuestions, $deletedQuestions],
            ])->render();

        return Command::SUCCESS;
    }
}
