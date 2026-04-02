<?php

namespace App\Api\Serializer\Normalizer;

use App\Service\Filter\Interface\FilterAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class EnumFilterNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'enum_resource_normalizer_already_called';

    /** @param \BackedEnum $object */
    public function normalize(mixed $object, ?string $format = null, array $context = []): string|int
    {
        $context[self::ALREADY_CALLED] = true;
        $normalized = $object->value;

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof \BackedEnum
            && $data instanceof FilterAwareInterface
            && false === ($context[self::ALREADY_CALLED] ?? false)
        ;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            FilterAwareInterface::class => false,
        ];
    }
}
