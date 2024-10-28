<?php

namespace App\Controller;

use App\Dto\SearchInput;
use App\Entity\EventType;
use App\Manager\EventManager;
use App\Serializer\JsonSerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SearchController
{
    /**
     * @param EventManager   $eventManager
     * @param JsonSerializer $serializer
     */
    public function __construct(
        private EventManager   $eventManager,
        private JsonSerializer $serializer
    ) {}

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    #[Route('/api/search', name: "api_search", methods: ['GET'])]
    public function searchCommits(Request $request): JsonResponse
    {
        $searchInput = $this->serializer->denormalize($request->query->all(), SearchInput::class);
        $countByType = $this->eventManager->countByType($searchInput);

        $data = [
            'meta' => [
                'totalEvents'       => $this->eventManager->countAll($searchInput),
                'totalPullRequests' => $countByType[EventType::PULL_REQUEST_EVENT] ?? 0,
                'totalCommits'      => $countByType[EventType::PUSH_EVENT] ?? 0,
                'totalComments'     => $countByType[EventType::COMMIT_COMMENT_EVENT] ?? 0,
            ],
            'data' => [
                'events' => $this->eventManager->getLatest($searchInput),
                'stats'  => $this->eventManager->statsByTypePerHour($searchInput)
            ]
        ];

        return new JsonResponse($data);
    }
}
