<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class EventDto implements DtoInterface
{
    /**
     * @var int
     * @Assert\NotBlank()
     */
    public int $id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public string $type;

    /**
     * @var ActorDto
     */
    public ActorDto $actor;

    /**
     * @var RepoDto
     */
    public RepoDto $repo;

    /**
     * @var array
     */
    public array $payload;

    /**
     * @var string|null
     */
    public ?string $comment = null;

    /**
     * @var \DateTimeInterface
     * @Assert\NotBlank()
     */
    public \DateTimeInterface $createdAt;
}
