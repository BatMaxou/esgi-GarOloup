<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Role;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

final class RoleNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'role_normalizer_already_called';

    public function __construct(
        private readonly StorageInterface $storage,
    ) {
    }

    /** @param Role $object */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $context[self::ALREADY_CALLED] = true;
        $normalized = $this->normalizer->normalize($object, $format, $context);

        if (!\is_array($normalized)) {
            throw new \LogicException(\sprintf('Normalized data should be an array for %s', Role::class));
        }

        if (isset($normalized['pictureName'])) {
            unset($normalized['pictureName']);
            $normalized['picture'] = $this->storage->resolveUri($object, 'picture');
        }

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Role && false === ($context[self::ALREADY_CALLED] ?? false);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Role::class => false,
        ];
    }
}
