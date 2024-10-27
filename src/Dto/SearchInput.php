<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;


class SearchInput
{
    /**
     * @var \DateTimeImmutable
     *
     * @Assert\NotBlank()
     */
    public \DateTimeImmutable $date;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public string $keyword;
}
