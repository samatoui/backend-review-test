<?php

namespace App\Assembler;

use App\Dto\DtoInterface;
use App\Dto\EventDto;
use App\Entity\Event;

/**
 * Clas  EventAssembler.
 */
class EventAssembler extends AbstractAssembler
{
    /**
     * @var RepoAssembler
     */
    private RepoAssembler $repoAssembler;

    /**
     * @var ActorAssembler
     */
    private ActorAssembler $actorAssembler;

    /**
     * EventAssembler constructor.
     *
     * @param RepoAssembler  $repoAssembler
     * @param ActorAssembler $actorAssembler
     */
    public function __construct(
        RepoAssembler $repoAssembler,
        ActorAssembler $actorAssembler,
    ) {
        $this->repoAssembler = $repoAssembler;
        $this->actorAssembler = $actorAssembler;
    }

    /**
     * @param Event $event
     *
     * @return DtoInterface
     *
     * @throws \Exception
     */
    public function transform($event): DtoInterface
    {
        if (!$event instanceof Event) {
            throw new \TypeError(sprintf(
                'Argument 1 passed to %s() must be an instance of %s, %s given.',
                __METHOD__,
                EventDto::class,
                \is_object($event) ? \get_class($event) : \gettype($event)
            ));
        }

        $eventDto            = new EventDto();
        $eventDto->id        = $event->id();
        $eventDto->type      = $event->getType();
        $eventDto->payload   = $event->getPayload();
        $eventDto->comment   = $event->getComment();
        $eventDto->createdAt = $event->getCreatedAt();
        $eventDto->actor     = $this->actorAssembler->transform($event->getActor());
        $eventDto->repo      = $this->repoAssembler->transform($event->getRepo());

        return $eventDto;
    }

    /**
     * @param DtoInterface $eventDto
     * @param Event|null   $event
     *
     * @throws \Exception
     *
     * @return Event
     */
    public function reverseTransform(DtoInterface $eventDto, $event = null): Event
    {
        if (!$eventDto instanceof EventDto) {
            throw new \TypeError(sprintf(
                'Argument 1 passed to %s() must be an instance of %s, %s given.',
                __METHOD__,
                EventDto::class,
                \is_object($eventDto) ? \get_class($eventDto) : \gettype($eventDto)
            ));
        }

        $event = $event ?? new Event(
            $eventDto->id,
            $eventDto->type,
            $this->actorAssembler->reverseTransform($eventDto->actor),
            $this->repoAssembler->reverseTransform($eventDto->repo),
            $eventDto->payload,
            $eventDto->createdAt,
            $eventDto->comment,
        );

        return $event;
    }
}