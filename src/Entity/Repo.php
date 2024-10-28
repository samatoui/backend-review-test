<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="repo")
 */
class Repo
{
    use EntityIdTrait;

    /**
     * @ORM\Column(type="string")
     */
    public string $name;

    /**
     * @ORM\Column(type="string")
     */
    public string $url;

    /**
     * @param int    $id
     * @param string $name
     * @param string $url
     */
    public function __construct(int $id, string $name, string $url)
    {
        $this->id   = $id;
        $this->name = $name;
        $this->url  = $url;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return self
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['id'], $data['name'], $data['url'])) {
            throw new \InvalidArgumentException("Invalid data for Repo entity");
        }

        return new self(
            (int) $data['id'],
            $data['name'],
            $data['url']
        );
    }
}
