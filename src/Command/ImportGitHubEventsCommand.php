<?php

declare(strict_types=1);

namespace App\Command;

use App\Events;
use App\Event\EventsBatchEvent;
use App\Service\GHArchiveService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * This command must import GitHub events.
 * You can add the parameters and code you want in this command to meet the need.
 */
class ImportGitHubEventsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:import-github-events';

    /**
     * @param GHArchiveService         $ghArchiveService
     * @param EventDispatcherInterface $dispatcher
     * @param int                      $eventBatchSize
     */
    public function __construct(
        private GHArchiveService         $ghArchiveService,
        private EventDispatcherInterface $dispatcher,
        private int                      $eventBatchSize,
    ) {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Import GH events')
             ->addArgument('date', InputArgument::REQUIRED, 'The date (YYYY-MM-DD)')
             ->addArgument('hour', InputArgument::REQUIRED, 'The hour (HH)')
             ->setHelp(<<<HELP
                The <info>%command.name%</info> command allows you to import GitHub events from GH Archive for a given date and hour.
                
                Usage:
                  <info>php %command.full_name% YYYY-MM-DD HH</info>
                
                Arguments:
                  <info>date</info>  The date for the events to import (in format YYYY-MM-DD)
                  <info>hour</info>  The hour for the events to import (in format HH, 24-hour format)
                
                Examples:
                  <info>php %command.full_name% 2025-10-27 14</info> (Imports events for 20 October 2024 at 2 PM)
                HELP
             );
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io   = new SymfonyStyle($input, $output);
        $date = $input->getArgument('date');
        $hour = $input->getArgument('hour');

        $io->title(sprintf('Importing events for %s at %sh', $date, $hour));

        try {
            $events = $this->ghArchiveService->downloadEvents($date, $hour);
            $bulk  = [];

            $progressBar = new ProgressBar($output);
            $progressBar->start();

            foreach ($events as $event) {
                $bulk[] = $event;

                if (count($bulk) >= $this->eventBatchSize) {
                    $this->dispatcher->dispatch(
                        new EventsBatchEvent($bulk),
                        Events::EVENTS_BATCH
                    );

                    $bulk = [];
                    $progressBar->advance();
                }
            }

            $progressBar->finish();
            $io->newLine(2);
            $io->success('Events successfully imported and processed.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->newLine(2);
            $io->error('Error occurred: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
