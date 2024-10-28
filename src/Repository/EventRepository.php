<?php

namespace App\Repository;

use App\Dto\SearchInput;
use App\Entity\Event;
use App\Entity\EventType;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class EventRepository extends ServiceEntityRepository implements RepositoryInterface
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @param SearchInput $searchInput
     * @return int
     * @throws \Exception
     */
    public function countAll(SearchInput $searchInput): int
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
            SELECT sum(count) as count
            FROM event
            WHERE date(created_at) = :date
            AND payload::text LIKE :keyword
        SQL;

        return (int) $connection->fetchOne($sql, [
            'date'    => $searchInput->date->format('Y-m-d'),
            'keyword' => '%' . $searchInput->keyword . '%',
        ]);
    }

    /**
     * @param SearchInput $searchInput
     * @return array
     * @throws \Exception
     */
    public function countByType(SearchInput $searchInput): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = <<<'SQL'
            SELECT type, sum(count) as count
            FROM event
            WHERE date(created_at) = :date
            AND payload::text LIKE :keyword
            GROUP BY type
        SQL;

        return $connection->fetchAllKeyValue($sql, [
            'date'    => $searchInput->date->format('Y-m-d'),
            'keyword' => '%' . $searchInput->keyword . '%',
        ]);
    }

    /**
     * @param SearchInput $searchInput
     * @return array
     * @throws \Exception
     */
    public function statsByTypePerHour(SearchInput $searchInput): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
            SELECT extract(hour from created_at) as hour, type, sum(count) as count
            FROM event
            WHERE date(created_at) = :date
            AND payload::text LIKE :keyword
            GROUP BY TYPE, EXTRACT(hour from created_at)
        SQL;

        $stats = $connection->fetchAllAssociative($sql, [
            'date'    => $searchInput->date->format('Y-m-d'),
            'keyword' => '%' . $searchInput->keyword . '%',
        ]);

        $data = array_fill(
            0,
            24,
            [
                EventType::PUSH_EVENT           => 0,
                EventType::PULL_REQUEST_EVENT   => 0,
                EventType::COMMIT_COMMENT_EVENT => 0
            ]
        );

        foreach ($stats as $stat) {
            $data[(int) $stat['hour']][$stat['type']] = $stat['count'];
        }

        return $data;
    }

    /**
     * @param SearchInput $searchInput
     * @return array
     * @throws \Exception
     */
    public function getLatest(SearchInput $searchInput): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
            SELECT e.type, row_to_json(r) AS repo
            FROM event e
            JOIN repo r ON e.repo_id = r.id
            WHERE date(e.created_at) = :date
            AND e.payload::text LIKE :keyword
        SQL;

        return $connection->fetchAllAssociative($sql, [
            'date'    => $searchInput->date->format('Y-m-d'),
            'keyword' => '%' . $searchInput->keyword . '%',
        ]);
    }

    /**
     * Insert a batch of events (EventDto).
     *
     * @param array $events
     *
     * @return void
     *
     * @throws Exception
     */
    public function insertOrUpdateBatchOfEvents(array $events): void
    {
        $connection = $this->getEntityManager()->getConnection();
        $connection->beginTransaction();

        gc_enable();

        $eventSql = <<<SQL
            INSERT INTO event (id, type, actor_id, repo_id, count, payload, comment, created_at)
            VALUES (:id, :type, :actor_id, :repo_id, :count, :payload, :comment, :created_at)
            ON CONFLICT (id) DO UPDATE SET 
                type      = EXCLUDED.type,
                actor_id  = EXCLUDED.actor_id,
                repo_id   = EXCLUDED.repo_id,
                count     = EXCLUDED.count,
                payload   = EXCLUDED.payload,
                comment   = EXCLUDED.comment,
                created_at = EXCLUDED.created_at
        SQL;

        $actorSql = <<<SQL
            INSERT INTO actor (id, login, url, avatar_url)
            VALUES (:id, :login, :url, :avatarUrl)
            ON CONFLICT (id) DO UPDATE SET 
                login      = EXCLUDED.login,
                url        = EXCLUDED.url,
                avatar_url = EXCLUDED.avatar_url
        SQL;

        $repoSql = <<<SQL
            INSERT INTO repo (id, name, url)
            VALUES (:id, :name, :url)
            ON CONFLICT (id) DO UPDATE SET 
                name = EXCLUDED.name,
                url  = EXCLUDED.url
        SQL;

        try {
            $actorStmt = $connection->prepare($actorSql);
            $repoStmt  = $connection->prepare($repoSql);
            $eventStmt = $connection->prepare($eventSql);

            foreach ($events as $event) {
                $actorStmt->executeQuery([
                    'id'        => $event->actor->id,
                    'login'     => $event->actor->login,
                    'url'       => $event->actor->url,
                    'avatarUrl' => $event->actor->avatarUrl,
                ]);

                $repoStmt->executeQuery([
                    'id'    => $event->repo->id,
                    'name'  => $event->repo->name,
                    'url'   => $event->repo->url,
                ]);

                $eventStmt->executeQuery([
                    'id'         => $event->id,
                    'type'       => $event->type,
                    'actor_id'   => $event->actor->id,
                    'repo_id'    => $event->repo->id,
                    'count'      => $event->payload['size'] ?? 1,
                    'payload'    => json_encode($event->payload),
                    'created_at' => $event->createdAt->format('Y-m-d H:i:s'),
                    'comment'    => $event->comment,
                ]);
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        } finally {
            $connection->close();
            gc_collect_cycles();
        }
    }
}
