<?php

declare(strict_types=1);

namespace App\Command;


use App\Service\SammModelGeneratorService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class SavePdfCommand extends Command
{
    protected static $defaultName = 'app:save-pdf';

    /**
     * SavePdfCommand constructor.
     * @param SammModelGeneratorService $sammModelGeneratorService
     * @param Filesystem                $filesystem
     * @param KernelInterface           $httpKernel
     */
    public function __construct(private SammModelGeneratorService $sammModelGeneratorService, private Filesystem $filesystem, private KernelInterface $httpKernel)
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription("Generates and saves the PDF");
    }

    /**
     * The order in which the steps are invoked should be right, otherwise it may fail for foreign key constraints.
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rawPdf = $this->sammModelGeneratorService->generate();

        $path = "{$this->httpKernel->getProjectDir()}/export/";
        if (!$this->filesystem->exists($path)) {
            $this->filesystem->mkdir($path);
        }
        $filename = "SAMM.pdf";
        $this->filesystem->dumpFile($path."/".$filename, $rawPdf);

        return Command::SUCCESS;
    }
}
