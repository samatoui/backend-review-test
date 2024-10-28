<?php

namespace App\Tests\Unit\Service;

use App\Service\GHArchiveService;
use App\Storage\SpoolStorage;
use App\Validator\DateHourValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GHArchiveServiceTest extends TestCase
{
    /**
     * @var GHArchiveService
     */
    private GHArchiveService $ghArchiveService;

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;

    /**
     * @var DateHourValidator
     */
    private DateHourValidator $dateHourValidator;

    /**
     * @var SpoolStorage
     */
    private SpoolStorage $spoolStorage;

    /**
     * @var string
     */
    private string $ghArchiveUrl = 'https://data.gharchive.org';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->httpClient        = $this->createMock(HttpClientInterface::class);
        $this->dateHourValidator = $this->createMock(DateHourValidator::class);
        $this->spoolStorage      = $this->createMock(SpoolStorage::class);
        $this->ghArchiveService  = new GHArchiveService(
            $this->httpClient,
            $this->dateHourValidator,
            $this->spoolStorage,
            $this->ghArchiveUrl
        );
    }

    public function testDownloadEventsSuccessful(): void
    {
        $date = '2024-10-27';
        $hour = '14';

        $this->dateHourValidator->expects($this->once())
            ->method('validate')
            ->with($date, $hour)
            ->willReturn([]);

        $this->assertEquals( $this->dateHourValidator->validate($date, $hour), []);
    }
}
