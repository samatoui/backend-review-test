<?php

namespace App\Controller;

use App\Dto\EventInput;
use App\Repository\ReadEventRepository;
use App\Repository\WriteEventRepository;
use App\Serializer\JsonSerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EventController
{
    /**
     * @var WriteEventRepository
     */
    private WriteEventRepository $writeEventRepository;

    /**
     * @var ReadEventRepository
     */
    private ReadEventRepository $readEventRepository;

    /**
     * @var JsonSerializer
     */
    private JsonSerializer $serializer;

    /**
     * @param WriteEventRepository $writeEventRepository
     * @param ReadEventRepository  $readEventRepository
     * @param JsonSerializer       $serializer
     */
    public function __construct(
        WriteEventRepository $writeEventRepository,
        ReadEventRepository $readEventRepository,
        JsonSerializer $serializer
    ) {
        $this->writeEventRepository = $writeEventRepository;
        $this->readEventRepository = $readEventRepository;
        $this->serializer          = $serializer;
    }

    /**
     * @param Request            $request
     * @param int                $id
     * @param ValidatorInterface $validator
     *
     * @return Response
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    #[Route('/api/event/{id}/update', name: "api_commit_update", methods: ['PUT'])]
    public function update(Request $request, int $id, ValidatorInterface $validator): Response
    {
        $eventInput = $this->serializer->denormalize(
            \json_decode($request->getContent(), true),
            EventInput::class
        );
        $errors = $validator->validate($eventInput);

        if (\count($errors) > 0) {
            return new JsonResponse(
                ['message' => $errors->get(0)->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        if($this->readEventRepository->exist($id) === false) {
            return new JsonResponse(
                ['message' => sprintf('Event identified by %d not found !', $id)],
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            $this->writeEventRepository->update($eventInput, $id);
        } catch (\Exception $exception) {
            return new Response(null, Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
