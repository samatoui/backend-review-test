<?php

namespace App\EventListener;

use App\Dto\EventDto;
use App\Events;
use App\Event\EventsBatchEvent;
use App\Manager\EventManager;
use App\Serializer\JsonSerializer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class EventsBatchEventListener implements EventSubscriberInterface
{
    /**
     * @param EventManager   $em
     * @param JsonSerializer $serializer
     */
    public function __construct(
        private EventManager   $em,
        private JsonSerializer $serializer,
    ) {}

    /**
     * Declares the events listened to by this listener
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::EVENTS_BATCH => 'onEventsBatch',
        ];
    }

    /**
     * Trait a bulk of events
     *
     * @param EventsBatchEvent $event
     * @throws \Exception
     * @throws ExceptionInterface
     */
    public function onEventsBatch(EventsBatchEvent $event): void
    {
        $events = [];

        foreach ($event->getEvents() as $object) {
            $events[] = $this->serializer->denormalize($object, EventDto::class);
        }

        $this->em->insertOrUpdateBatchOfEvents($events);
    }
}
