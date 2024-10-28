<?php

namespace App\Manager;

use App\Entity\Repo;
use App\Repository\RepoRepository;

/**
 * Class RepoManager.
 */
class RepoManager extends BaseManager
{
    /**
     * RepoManager constructor.
     *
     * @param RepoRepository $repoRepository
     */
    public function __construct(RepoRepository $repoRepository)
    {
        parent::__construct($repoRepository);
    }

    /**
     * @param int $id
     *
     * @return Repo|null
     */
    public function findOneById(int $id): ?Repo
    {
        return $this->entityRepository->findOneBy(['id' => $id]);
    }
}
