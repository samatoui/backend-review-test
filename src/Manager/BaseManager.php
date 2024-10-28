<?php

namespace App\Manager;

use App\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class BaseManager.
 */
class BaseManager
{
    /**
     * @var EntityManagerInterface
     *
     * @required
     */
    public EntityManagerInterface $em;

    /**
     * @var RepositoryInterface
     */
    protected RepositoryInterface $entityRepository;

    /**
     * BaseManager constructor.
     *
     */
    public function __construct(RepositoryInterface $entityRepository = null)
    {
        $this->entityRepository = $entityRepository;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @param $entity
     *
     * @return object
     */
    public function save($entity): object
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    /**
     * @param $entity
     */
    public function delete($entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * @param $entity
     */
    public function remove($entity): void
    {
        $this->em->remove($entity);
    }

    /**
     * @param $entity
     */
    public function persist($entity): void
    {
        $this->em->persist($entity);
    }

    /**
     * @param $entity
     */
    public function refresh($entity): void
    {
        $this->em->refresh($entity);
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->em->flush();
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->em->clear();
    }
}
