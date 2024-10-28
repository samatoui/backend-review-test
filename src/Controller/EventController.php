<?php

namespace App\Controller;

use App\Assembler\EventAssembler;
use App\Manager\EventManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EventController
{
    /**
     * @param EventManager   $eventManager
     * @param EventAssembler $eventAssembler
     */
    public function __construct(
        private EventManager   $eventManager,
        private EventAssembler $eventAssembler
    ) {}

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
        if (null === $event = $this->eventManager->findOneById($id)) {
            return new JsonResponse(
                ['message' => sprintf('Event identified by %d not found !', $id)],
                Response::HTTP_NOT_FOUND
            );
        }

        $eventDto = $this->eventAssembler->transformAndPatch($event, $request->request->all());
        $errors   = $validator->validate($eventDto);

        if (\count($errors) > 0) {
            return new JsonResponse(
                ['message' => $errors->get(0)->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $event = $this->eventAssembler->reverseTransform($eventDto, $event);
            $this->eventManager->save($event);
        } catch (\Exception $exception) {
            return new Response(null, Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
