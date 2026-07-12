<?php

namespace App\Api\Serializer\Normalizer;

use App\Enum\Game\GameTeamEnum;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class GameTeamEnumDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): GameTeamEnum
    {
        if ($data instanceof GameTeamEnum) {
            return $data;
        }

        if (!\is_string($data) || null === $team = GameTeamEnum::tryFrom($data)) {
            throw NotNormalizableValueException::createForUnexpectedDataType(
                \sprintf('The value "%s" is not a valid %s.', \is_scalar($data) ? (string) $data : \get_debug_type($data), GameTeamEnum::class),
                $data,
                [GameTeamEnum::class],
                $context['deserialization_path'] ?? null,
            );
        }

        return $team;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return GameTeamEnum::class === $type;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            GameTeamEnum::class => true,
        ];
    }
}