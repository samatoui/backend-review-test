<?php

namespace App\Manager;

use App\Entity\Actor;
use App\Repository\ActorRepository;

/**
 * Class ActorManager.
 */
class ActorManager extends BaseManager
{
    /**
     * ActorManager constructor.
     *
     * @param ActorRepository $actorRepository
     */
    public function __construct(ActorRepository $actorRepository)
    {
        parent::__construct($actorRepository);
    }

    /**
     * @param int $id
     *
     * @return Actor|null
     */
    public function findOneById(int $id): ?Actor
    {
        return $this->entityRepository->findOneBy(['id' => $id]);
    }
}
