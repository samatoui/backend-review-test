<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RepoDto implements DtoInterface
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
    public string $name;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public string $url;
}
