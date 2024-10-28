<?php

namespace App\Tests\Unit\Storage;

use App\Storage\SpoolStorage;
use Gaufrette\Filesystem as GaufretteFilesystem;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class SpoolStorageTest extends TestCase
{
    private SpoolStorage $spoolStorage;
    private FilesystemMap $fileSystemMap;
    private Filesystem $fileSystem;
    private GaufretteFilesystem $gaufretteFileSystem;
    private string $spoolDir = '/tmp/spool';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->fileSystem = $this->createMock(Filesystem::class);
        $this->fileSystemMap = $this->createMock(FilesystemMap::class);
        $this->gaufretteFileSystem = $this->createMock(GaufretteFilesystem::class);

        $this->fileSystemMap->method('get')
            ->with('custom_uploads_spool_fs')
            ->willReturn($this->gaufretteFileSystem);

        $this->spoolStorage = new SpoolStorage($this->fileSystem, $this->fileSystemMap, $this->spoolDir);
    }

    /**
     * @return void
     */
    public function testReadFromSpool()
    {
        $fileName = 'test.txt';
        $data     = 'Some test data';

        $this->gaufretteFileSystem->expects($this->once())
            ->method('has')
            ->with($fileName)
            ->willReturn(true);

        $this->gaufretteFileSystem->expects($this->once())
            ->method('read')
            ->with($fileName)
            ->willReturn($data);

        $result = $this->spoolStorage->readFromSpool($fileName);
        $this->assertEquals($data, $result);
    }
}
