<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class EventInput
{
    /**
     * @Assert\Length(min=20)
     */
    public ?string $comment;

    /**
     * @param string|null $comment
     */
    public function __construct(?string $comment)
    {
        $this->comment = $comment;
    }
}
