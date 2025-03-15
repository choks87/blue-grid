<?php
declare(strict_types=1);

namespace BlueGrid\Command;

use BlueGrid\Criteria\Criteria;
use BlueGrid\Dto\PaginationDto;
use BlueGrid\Enum\TransformType;
use BlueGrid\Repository\TreeRepository;
use BlueGrid\Service\DataLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name       : 'app:load-external-data',
    description: 'Add a short description for your command',
)]
class LoadExternalDataCommand extends Command
{
    public function __construct(
        private readonly DataLoader $dataLoader,
        private readonly TreeRepository $treeRepository,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $questionHelper = new QuestionHelper();
        $question       = new ConfirmationQuestion(
            'This will truncate all data in database related to Hosts, Files and Directories. Continue?',
            true,
        );

        if (false === $questionHelper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $io->note('Loading data...');
        $this->dataLoader->load();

        $io->success('Data successfully loaded and stored to database.');

        $io->info('Warming up cache.');

        foreach (TransformType::cases() as $transformType) {
            $this->treeRepository->getTree(new Criteria($transformType, new PaginationDto()));
        }

        $io->success('Done.');

        return Command::SUCCESS;
    }
}
