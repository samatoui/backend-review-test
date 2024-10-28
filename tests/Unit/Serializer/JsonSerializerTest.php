<?php

namespace App\Tests\Unit\Serializer;

use App\Dto\ActorDto;
use App\Dto\EventDto;
use App\Dto\RepoDto;
use App\Entity\EventType;
use App\Serializer\JsonSerializer;
use PHPUnit\Framework\TestCase;

class JsonSerializerTest extends TestCase
{
    /**
     * @var JsonSerializer
     */
    private JsonSerializer $serializer;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->serializer = new JsonSerializer();
    }

    /**
     * @return void
     */
    public function testSerialize(): void
    {
        $actor            = new ActorDto();
        $actor->id        = 4;
        $actor->login     = 'login';
        $actor->url       = 'url1';
        $actor->avatarUrl = 'avatarUrl1';

        $repo       = new RepoDto();
        $repo->id   = 3;
        $repo->name = 'repo';
        $repo->url  = 'url';

        $event            = new EventDto();
        $event->id        = 10;
        $event->type      = EventType::ISSUE_COMMENT_EVENT;
        $event->actor     = $actor;
        $event->repo      = $repo;
        $event->payload   = ['key' => 'test'];
        $event->createdAt = new \DateTimeImmutable('2024-10-25T14:00:00Z');

        $json = $this->serializer->serialize($event);

        $this->assertJson($json);
        $this->assertStringContainsString('"type": "'.EventType::ISSUE_COMMENT_EVENT.'"', $json);
        $this->assertStringContainsString('"actor":', $json);
        $this->assertStringContainsString('"repo":', $json);
    }

    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testDenormalize(): void
    {
        $data = [
            'id'    => 1,
            'type'  => EventType::CHECK_SUITE_EVENT,
            'actor' => [
                'id'        => 123,
                'login'     => 'login1',
                'url'       => 'url1',
                'avatarUrl' => 'avatarUrl1',
            ],
            'repo' => [
                'id'   => 12,
                'name' => 'repo1',
                'url'  => 'url1',
            ],
            'payload' => [
                'key' => 'test',
            ],
            'create_at' => '2024-10-25T14:00:00Z',
        ];

        $event = $this->serializer->denormalize($data, EventDto::class);

        $this->assertInstanceOf(EventDto::class, $event);
        $this->assertEquals(EventType::CHECK_SUITE_EVENT, $event->type);
        $this->assertInstanceOf(ActorDto::class, $event->actor);
        $this->assertInstanceOf(RepoDto::class, $event->repo);
    }
}
