<?php

declare(strict_types=1);

namespace App\Command;

use App\Ingestion\IngestionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:ingest', description: 'Ingère un fichier Markdown')]
final class IngestCommand extends Command
{
    public function __construct(private readonly IngestionService $ingestion)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Chemin du fichier .md');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        $id = $this->ingestion->ingestMarkdown(basename($file), (string) file_get_contents($file));

        $output->writeln("Document $id ingéré.");

        return Command::SUCCESS;
    }
}