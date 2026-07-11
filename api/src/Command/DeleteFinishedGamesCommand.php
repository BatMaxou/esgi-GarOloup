<?php

namespace App\Command;

use App\Repository\Game\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:game:delete-finished',
    description: 'Delete finished games',
)]
class DeleteFinishedGamesCommand extends Command
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'List all games to delete');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');

        $games = $this->gameRepository->findFinished();
        if (0 === \count($games)) {
            $io->success('No games to delete');

            return Command::SUCCESS;
        }

        $io->note(\sprintf('%d game(s) to delete', \count($games)));

        foreach ($games as $game) {
            $io->writeln(\sprintf(' - %s', (string) $game->getId()));

            if (!$dryRun) {
                $this->em->remove($game);
            }
        }

        if ($dryRun) {
            $io->success(\sprintf('[dry-run] %s game(s) to delete', \count($games)));

            return Command::SUCCESS;
        }

        $this->em->flush();

        $io->success(\sprintf('%s game(s) deleted', \count($games)));

        return Command::SUCCESS;
    }
}
