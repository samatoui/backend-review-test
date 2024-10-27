<?php

namespace App\Tests\Unit\Command;

use App\Command\ImportGitHubEventsCommand;
use App\Events;
use App\Event\EventsBatchEvent;
use App\Service\GHArchiveService;
use PHPUnit\Framework\MockObject\MockObject as MockObjectAlias;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ImportGitHubEventsCommandTest extends TestCase
{
    /**
     * @var MockObjectAlias
     */
    private MockObjectAlias $ghArchiveService;

    /**
     * @var MockObjectAlias
     */
    private MockObjectAlias $eventDispatcher;

    /**
     * @var int
     */
    private int $eventBatchSize = 100;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->ghArchiveService = $this->createMock(GHArchiveService::class);
        $this->eventDispatcher  = $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @return void
     */
    public function testExecuteSuccessfully(): void
    {
        $date = '2024-10-25';
        $hour = '14';

        // Mock the downloadEvents method to return an iterable
        $this->ghArchiveService->method('downloadEvents')
            ->with($date, $hour)
            ->willReturn($this->getMockedEvents());

        $this->eventDispatcher->expects($this->atLeastOnce())
            ->method('dispatch')
            ->with($this->isInstanceOf(EventsBatchEvent::class), Events::EVENTS_BATCH);

        $command = new ImportGitHubEventsCommand(
            $this->ghArchiveService,
            $this->eventDispatcher,
            2
        );

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($application->find('app:import-github-events'));
        $commandTester->execute([
            'date' => $date,
            'hour' => $hour,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Importing events for 2024-10-25 at 14h', $output);
        $this->assertStringContainsString('Events successfully imported and processed.', $output);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteWithError()
    {
        $date = '2024-10-25';
        $hour = '14';

        $this->ghArchiveService->method('downloadEvents')
            ->with($date, $hour)
            ->willThrowException(new \RuntimeException('An error occurred'));

        $command = new ImportGitHubEventsCommand(
            $this->ghArchiveService,
            $this->eventDispatcher,
            $this->eventBatchSize
        );

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($application->find('app:import-github-events'));
        $commandTester->execute([
            'date' => $date,
            'hour' => $hour,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Error occurred: An error occurred', $output);

        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    /**
     * @return \Generator
     */
    private function getMockedEvents(): \Generator
    {
        for ($i = 0; $i < 4; $i++) {
            yield [
                'id'      => $i,
                'type'    => 'IssueCommentEvent',
                'actor'   => ['id' => 123, 'login' => 'user' . $i],
                'repo'    => ['id' => 456, 'name'  => 'repo' . $i],
                'payload' => [],
                'createAt' => '2024-10-25T14:00:00Z',
            ];
        }
    }
}
