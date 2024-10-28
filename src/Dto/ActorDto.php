<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ActorDto implements DtoInterface
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
    public string $login;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public string $url;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public string $avatarUrl;
}
