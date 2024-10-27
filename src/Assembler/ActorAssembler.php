<?php

namespace App\Assembler;

use App\Dto\DtoInterface;
use App\Dto\ActorDto;
use App\Entity\Actor;

/**
 * Clas ActorAssembler.
 */
class ActorAssembler extends AbstractAssembler
{
    /**
     * @param Actor $actor
     *
     * @return DtoInterface
     *
     * @throws \Exception
     */
    public function transform($actor): DtoInterface
    {
        if (!$actor instanceof Actor) {
            throw new \TypeError(sprintf(
                'Argument 1 passed to %s() must be an instance of %s, %s given.',
                __METHOD__,
                ActorDto::class,
                \is_object($actor) ? \get_class($actor) : \gettype($actor)
            ));
        }

        $actorDto            = new ActorDto();
        $actorDto->id        = $actor->id();
        $actorDto->login     = $actor->getLogin();
        $actorDto->url       = $actor->getUrl();
        $actorDto->avatarUrl = $actor->getAvatarUrl();

        return $actorDto;
    }

    /**
     * @param DtoInterface $actorDto
     * @param Actor|null    $actor
     *
     * @throws \Exception
     *
     * @return Actor
     */
    public function reverseTransform(DtoInterface $actorDto, $actor = null): Actor
    {
        if (!$actorDto instanceof ActorDto) {
            throw new \TypeError(sprintf(
                'Argument 1 passed to %s() must be an instance of %s, %s given.',
                __METHOD__,
                ActorDto::class,
                \is_object($actorDto) ? \get_class($actorDto) : \gettype($actorDto)
            ));
        }

        $actor = $actor ?? new Actor(
            $actorDto->id,
            $actorDto->login,
            $actorDto->url,
            $actorDto->avatarUrl,
        );

        return $actor;
    }
}
