<?php

namespace App\Controller;

use App\Dto\SearchInput;
use App\Repository\ReadEventRepository;
use App\Serializer\JsonSerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SearchController
{
    /**
     * @var ReadEventRepository
     */
    private ReadEventRepository $repository;

    /**
     * @var JsonSerializer
     */
    private JsonSerializer $serializer;

    /**
     * @param ReadEventRepository $repository
     * @param JsonSerializer      $serializer
     */
    public function __construct(
        ReadEventRepository $repository,
        JsonSerializer  $serializer
    ) {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

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
        $countByType = $this->repository->countByType($searchInput);

        $data = [
            'meta' => [
                'totalEvents'       => $this->repository->countAll($searchInput),
                'totalPullRequests' => $countByType['pullRequest'] ?? 0,
                'totalCommits'      => $countByType['commit'] ?? 0,
                'totalComments'     => $countByType['comment'] ?? 0,
            ],
            'data' => [
                'events' => $this->repository->getLatest($searchInput),
                'stats'  => $this->repository->statsByTypePerHour($searchInput)
            ]
        ];

        return new JsonResponse($data);
    }
}
