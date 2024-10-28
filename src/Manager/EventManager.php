<?php

namespace App\Manager;

use App\Dto\SearchInput;
use App\Entity\Event;
use App\Repository\EventRepository;

/**
 * Class EventManager.
 */
class EventManager extends BaseManager
{
    /**
     * EventManager constructor.
     *
     * @param EventRepository $eventRepository
     */
    public function __construct(EventRepository $eventRepository)
    {
        parent::__construct($eventRepository);
    }

    /**
     * Count all events for a given date and keyword.
     *
     * @param SearchInput $searchInput
     * @return int
     * @throws \Exception
     */
    public function  countAll(SearchInput $searchInput): int
    {
        return $this->entityRepository->countAll($searchInput);
    }

    /**
     * Count events by type for a given date and keyword.
     *
     * @param SearchInput $searchInput
     * @return array
     * @throws \Exception
     */
    public function  countByType(SearchInput $searchInput): array
    {
        return $this->entityRepository->countByType($searchInput);
    }

    /**
     * Get event statistics per hour by type for a given date and keyword.
     *
     * @param SearchInput $searchInput
     * @return array
     * @throws \Exception
     */
    public function  statsByTypePerHour(SearchInput $searchInput): array
    {
        return $this->entityRepository->statsByTypePerHour($searchInput);
    }

    /**
     * Get the latest events with their associated repo.
     *
     * @param SearchInput $searchInput
     * @return array
     * @throws \Exception
     */
    public function  getLatest(SearchInput $searchInput): array
    {
        return $this->entityRepository->getLatest($searchInput);
    }

    /**
     * Get event by ID.
     *
     * @param int $id
     * @return Event|null
     */
    public function findOneById(int $id): ?Event
    {
        return $this->entityRepository->findOneBy(['id' => $id]);
    }

    /**
     * Check if an event exists by its ID.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function  exist(int $id): bool
    {
        return (bool) $this->findOneById($id);
    }

    /**
     * Insert a batch of events (EventDto).
     *
     * @param array $events
     * @return void
     * @throws \Exception
     */
    public function insertOrUpdateBatchOfEvents(array $events): void
    {
        $this->entityRepository->insertOrUpdateBatchOfEvents($events);
    }
}
