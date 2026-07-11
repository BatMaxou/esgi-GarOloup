<?php

namespace App\Api\Serializer\Normalizer\Game;

use App\Entity\Game\Recap;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RecapNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'recap_normalizer_already_called';

    /**
     * @param Recap $data
     *
     * @return mixed[]
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $context[self::ALREADY_CALLED] = true;

        $normalized = $this->normalizer->normalize($data, $format, $context);
        if (!\is_array($normalized)) {
            throw new \LogicException('Normalizer should return an array.');
        }

        $normalized['winningTeam'] = $data->getWinningTeam()->value;

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return
            $data instanceof Recap
            && !(isset($context[self::ALREADY_CALLED]) && $context[self::ALREADY_CALLED])
            && isset($context['groups'])
            && (
                (\is_string($context['groups']) && 'recap:read' === $context['groups'])
                || (\is_array($context['groups']) && \in_array('recap:read', $context['groups'], true))
            );
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Recap::class => false];
    }
}
