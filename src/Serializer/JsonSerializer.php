<?php

namespace App\Serializer;

use App\Entity\Event;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ConstraintViolationListNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class JsonSerializer.
 */
class JsonSerializer
{
    /**
     * @var Serializer
     */
    private Serializer $serializer;

    /**
     * JsonSerializer constructor.
     */
    public function __construct()
    {
        $encoders             = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader(new AnnotationReader()));
        $normalizers          = [
            new DateTimeNormalizer(),
            new ObjectNormalizer($classMetadataFactory, new CamelCaseToSnakeCaseNameConverter(), null, new PhpDocExtractor()),
            new ConstraintViolationListNormalizer([], new CamelCaseToSnakeCaseNameConverter()),
            new ArrayDenormalizer(),
        ];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @param $data
     *
     * @return bool|float|int|string
     */
    public function serialize($data): float|bool|int|string
    {
        $context = [JsonEncode::OPTIONS => JSON_PRETTY_PRINT];

        return $this->serializer->serialize($data, 'json', $context);
    }

    /**
     * @param $data
     * @param $type
     * @param null  $object
     *
     * @return object
     *
     * @throws ExceptionInterface
     */
    public function denormalize($data, $type, $object = null): mixed
    {
        $context = [
            AbstractNormalizer::OBJECT_TO_POPULATE             => $object,
            AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
        ];

        return $this->serializer->denormalize($data, $type, 'json', $context);
    }
}
