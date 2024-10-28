<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="actor")
 */
class Actor
{
    use EntityIdTrait;

    /**
     * @ORM\Column(type="string")
     */
    public string $login;

    /**
     * @ORM\Column(type="string")
     */
    public string $url;

    /**
     * @ORM\Column(type="string")
     */
    public string $avatarUrl;

    /**
     * @param int    $id
     * @param string $login
     * @param string $url
     * @param string $avatarUrl
     */
    public function __construct(int $id, string $login, string $url, string $avatarUrl)
    {
        $this->id        = $id;
        $this->login     = $login;
        $this->url       = $url;
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * @return string
     */
    public function login(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return self
     */
    public function setLogin(string $login): self
    {
        $this->login = $login;

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
     * @return string
     */
    public function avatarUrl(): string
    {
        return $this->avatarUrl;
    }

    /**
     * @param string $avatarUrl
     *
     * @return self
     */
    public function setAvatarUrl(string $avatarUrl): self
    {
        $this->avatarUrl = $avatarUrl;
        return $this;
    }

    /**
     * @param array $data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['id'], $data['login'], $data['url'], $data['avatar_url'])) {
            throw new \InvalidArgumentException("Invalid data for Actor entity");
        }

        return new self(
            (int) $data['id'],
            $data['login'],
            $data['url'],
            $data['avatar_url']
        );
    }
}
