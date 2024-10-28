<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class EventsBatchEvent extends Event
{
    /**
     * @var array
     */
    private array $events;

    /**
     * @param array $events
     */
    public function __construct(array $events)
    {
        $this->events = $events;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param array $events
     * @return self
     */
    public function setEvents(array $events): self
    {
        $this->events = $events;

        return $this;
    }
}
