<?php

namespace App\Storage;

use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Symfony\Component\Filesystem\Filesystem;
use Gaufrette\Filesystem as GaufretteFilesystem;

/**
 * Class SpoolStorage.
 */
class SpoolStorage
{
    /**
     * @var Filesystem
     */
    private Filesystem $fileSystem;

    /**
     * @var FilesystemMap
     */
    private FilesystemMap $fileSystemMap;

    /**
     * @var string
     */
    private string $spoolDir;

    /**
     * SpoolStorage constructor.
     *
     * @param Filesystem    $fileSystem
     * @param FilesystemMap $fileSystemMap
     * @param string        $spoolDir
     */
    public function __construct(Filesystem $fileSystem, FilesystemMap $fileSystemMap, string $spoolDir)
    {
        $this->fileSystem    = $fileSystem;
        $this->fileSystemMap = $fileSystemMap;
        $this->spoolDir      = rtrim($spoolDir, '/');
    }

    /**
     * @return GaufretteFilesystem
     */
    public function getSpoolFileSystem(): GaufretteFilesystem
    {
        return $this->fileSystemMap->get('custom_uploads_spool_fs');
    }

    /**
     * @return self
     */
    public function ensureSpoolDirectoryExists(): self
    {
        if (!$this->fileSystem->exists($this->spoolDir)) {
            $this->fileSystem->mkdir($this->spoolDir, 0777);
        }

        return $this;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public function getSpoolFilePath(string $fileName): string
    {
        return sprintf('%s/%s', $this->spoolDir, $fileName);
    }

    /**
     * @param string $fileName
     * @return resource
     */
    public function openForWrite(string $fileName)
    {
        $filePath = $this->getSpoolFilePath($fileName);
        $file     = fopen($filePath, 'w+');

        if ($file === false) {
            throw new \RuntimeException("Failed to open file for writing: $filePath");
        }

        return $file;
    }

    /**
     * Ouvre un fichier pour lecture.
     *
     * @param string $fileName
     * @return resource
     */
    public function openForRead(string $fileName)
    {
        $filePath = $this->getSpoolFilePath($fileName);
        $file     = fopen($filePath, 'r');

        if ($file === false) {
            throw new \RuntimeException("Failed to open file for reading: $filePath");
        }

        return $file;
    }

    /**
     * @param $file
     *
     * @return $this
     */
    public function closeFile($file): self
    {
        if (is_resource($file)) {
            fclose($file);
        }

        return $this;
    }

    /**
     * @param string $fileName
     * @param string $data
     */
    public function saveToSpool(string $fileName, string $data): void
    {
        $this->ensureSpoolDirectoryExists()
             ->getSpoolFileSystem()->write($fileName, $data, true);
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public function readFromSpool(string $fileName): string
    {
        $fs = $this->getSpoolFileSystem();

        if (!$fs->has($fileName)) {
            throw new \RuntimeException("File not found in spool: $fileName");
        }

        return $fs->read($fileName);
    }

    /**
     * @param string $fileName
     */
    public function removeFromSpool(string $fileName): void
    {
        $fs = $this->getSpoolFileSystem();

        if ($fs->has($fileName)) {
            $fs->delete($fileName);
        }
    }

    /**
     * @param array $fileNames
     */
    public function removeMultipleFromSpool(array $fileNames): void
    {
        foreach ($fileNames as $fileName) {
            $this->removeFromSpool($fileName);
        }
    }
}
