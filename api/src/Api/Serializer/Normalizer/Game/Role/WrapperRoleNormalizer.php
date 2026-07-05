<?php

namespace App\Api\Serializer\Normalizer\Game\Role;

use App\Entity\Game\Role\Interface\WrapperRoleInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class WrapperRoleNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'wrapper_role_normalizer_already_called';

    /** @param WrapperRoleInterface $object */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $originalData = $this->normalizer->normalize($object->getOriginalRole(), $format, $context);

        $wrapperContext = $context;
        $wrapperContext[self::ALREADY_CALLED] = true;
        $wrapperData = $this->normalizer->normalize($object, $format, $wrapperContext);

        if (!\is_array($originalData) || !\is_array($wrapperData)) {
            throw new \LogicException(\sprintf('Normalized data should be an array for %s', WrapperRoleInterface::class));
        }

        return \array_merge($originalData, $wrapperData);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof WrapperRoleInterface && false === ($context[self::ALREADY_CALLED] ?? false);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            WrapperRoleInterface::class => false,
        ];
    }
}
