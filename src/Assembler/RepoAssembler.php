<?php

namespace App\Assembler;

use App\Dto\DtoInterface;
use App\Dto\RepoDto;
use App\Entity\Repo;

/**
 * Clas RepoAssembler.
 */
class RepoAssembler extends AbstractAssembler
{
    /**
     * @param Repo $repo
     *
     * @return DtoInterface
     *
     * @throws \Exception
     */
    public function transform($repo): DtoInterface
    {
        if (!$repo instanceof Repo) {
            throw new \TypeError(sprintf(
                'Argument 1 passed to %s() must be an instance of %s, %s given.',
                __METHOD__,
                RepoDto::class,
                \is_object($repo) ? \get_class($repo) : \gettype($repo)
            ));
        }

        $repoDto       = new RepoDto();
        $repoDto->id   = $repo->id();
        $repoDto->name = $repo->name();
        $repoDto->url  = $repo->url();

        return $repoDto;
    }

    /**
     * @param DtoInterface $repoDto
     * @param Repo|null    $repo
     *
     * @throws \Exception
     *
     * @return Repo
     */
    public function reverseTransform(DtoInterface $repoDto, $repo = null): Repo
    {
        if (!$repoDto instanceof RepoDto) {
            throw new \TypeError(sprintf(
                'Argument 1 passed to %s() must be an instance of %s, %s given.',
                __METHOD__,
                RepoDto::class,
                \is_object($repoDto) ? \get_class($repoDto) : \gettype($repoDto)
            ));
        }

        return $repo ?? new Repo(
            $repoDto->id,
            $repoDto->name,
            $repoDto->url,
        );
    }
}
