<?php

declare(strict_types=1);

namespace App\Command;

use App\Rag\RetrievalService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:search', description: 'Recherche les passages pertinents')]
final class SearchCommand extends Command
{
    public function __construct(private readonly RetrievalService $retrieval)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('question', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->retrieval->search((string) $input->getArgument('question')) as $i => $hit) {
            $output->writeln(sprintf(
                '%d. [%s #%d] distance=%.3f : %s',
                $i + 1,
                $hit['title'],
                $hit['position'],
                $hit['distance'],
                mb_substr(str_replace("\n", ' ', $hit['content']), 0, 80),
            ));
        }

        return Command::SUCCESS;
    }
}