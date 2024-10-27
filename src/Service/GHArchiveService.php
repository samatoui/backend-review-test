<?php

namespace App\Service;

use App\Storage\SpoolStorage;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Validator\DateHourValidator;

class GHArchiveService
{
    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $client;

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
    private string $ghArchiveUrl;

    /**
     * @param HttpClientInterface $client
     * @param DateHourValidator   $dateHourValidator
     * @param SpoolStorage        $spoolStorage
     * @param string              $ghArchiveUrl
     */
    public function __construct(
        HttpClientInterface $client,
        DateHourValidator   $dateHourValidator,
        SpoolStorage        $spoolStorage,
        string              $ghArchiveUrl
    )
    {
        $this->client            = $client;
        $this->dateHourValidator = $dateHourValidator;
        $this->spoolStorage      = $spoolStorage;
        $this->ghArchiveUrl      = $ghArchiveUrl;
    }

    /**
     * @param string $date
     * @param string $hour
     *
     * @return iterable
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function downloadEvents(string $date, string $hour): iterable
    {
        if (count($errors = $this->dateHourValidator->validate($date, $hour)) > 0) {
            throw new \InvalidArgumentException(implode(', ', $errors));
        }

        $compressedFileName = sprintf('github-events-%s-%s.json.gz', $date, $hour);
        $jsonFileName       = str_replace('.gz', '.json', $compressedFileName);

        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/%s-%s.json.gz', $this->ghArchiveUrl, $date, $hour),
                ['buffer' => false]
            );

            $compressedFile = $this->spoolStorage->openForWrite($compressedFileName);

            foreach ($this->client->stream($response) as $chunk) {
                fwrite($compressedFile, $chunk->getContent());
            }

            $this->spoolStorage->closeFile($compressedFile);

            if (!$gzFile = gzopen($this->spoolStorage->getSpoolFilePath($compressedFileName), 'rb')) {
                throw new \RuntimeException('Failed to open the GZIP file for decompression.');
            }

            $jsonFile = $this->spoolStorage->openForWrite($jsonFileName);

            while (!gzeof($gzFile)) {
                fwrite($jsonFile, gzread($gzFile, 8192));
            }

            gzclose($gzFile);
            $this->spoolStorage->closeFile($jsonFile);

            $jsonFile = $this->spoolStorage->openForRead($jsonFileName);

            while (($line = fgets($jsonFile)) !== false) {
                $event = json_decode($line, true);

                if ($event === null && json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException('Failed to decode JSON. Error: ' . json_last_error_msg());
                }

                yield $event;
            }

            $this->spoolStorage->closeFile($jsonFile);

        } catch (\Exception $e) {
            throw new \RuntimeException('An error occurred: ' . $e->getMessage());
        } finally {
            $this->spoolStorage->removeMultipleFromSpool([$compressedFileName, $jsonFileName]);
        }
    }
}
