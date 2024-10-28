<?php

namespace App\Assembler;

use App\Dto\DtoInterface;
use App\Serializer\JsonSerializer;

/**
 * Class AbstractAssembler.
 */
abstract class AbstractAssembler
{
    /**
     * @param JsonSerializer $serializer
     */
    public function __construct(protected JsonSerializer $serializer)
    {}


    /**
     * @return JsonSerializer
     */
    public function getSerializer(): JsonSerializer
    {
        return $this->serializer;
    }

    /**
     * @param mixed $entity
     *
     * @return DtoInterface
     */
    abstract public function transform($entity): DtoInterface;

    /**
     * @param DtoInterface $dto
     * @param mixed|null   $entity
     *
     * @throws \Exception
     */
    abstract public function reverseTransform(DtoInterface $dto, $entity = null);

    /**
     * @param mixed $entity
     * @param array $params
     *
     * @return DtoInterface
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function transformAndPatch($entity, array $params): DtoInterface
    {
        return $this->patch($this->transform($entity), $params);
    }

    /**
     * @param DtoInterface $dto
     * @param array        $params
     *
     * @return DtoInterface
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    private function patch(DtoInterface $dto, array $params): DtoInterface
    {
        $this->serializer->denormalize($params, \get_class($dto), $dto);

        return $dto;
    }
}
