<?php

namespace App\EventListener;

use App\Assembler\EventAssembler;
use App\Dto\EventDto;
use App\Events;
use App\Event\EventsBatchEvent;
use App\Serializer\JsonSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class EventsBatchEventListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     *
     */
    private EntityManagerInterface $em;

    /**
     * @var JsonSerializer
     */
    private JsonSerializer $serializer;

    /**
     * @var EventAssembler
     */
    private EventAssembler $eventAssembler;

    /**
     * @param EntityManagerInterface $em
     * @param JsonSerializer         $serializer
     * @param EventAssembler         $eventAssembler
     */
    public function __construct(
        EntityManagerInterface $em,
        JsonSerializer $serializer,
        EventAssembler $eventAssembler,
    ) {
        $this->em             = $em;
        $this->serializer     = $serializer;
        $this->eventAssembler = $eventAssembler;
    }

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
     * @throws ExceptionInterface
     */
    public function onEventsBatch(EventsBatchEvent $event): void
    {
        foreach ($event->getEvents() as $object) {
            $eventDto= $this->serializer->denormalize($object, EventDto::class);
            $eventObj = $this->eventAssembler->reverseTransform($eventDto);
            $this->em->persist($eventObj);
        }

        $this->em->flush();
    }
}
