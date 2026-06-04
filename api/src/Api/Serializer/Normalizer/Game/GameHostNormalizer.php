<?php

namespace App\Api\Serializer\Normalizer\Game;

use App\Entity\Game\Game;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GameHostNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'game_host_normalizer_already_called';

    /**
     * @param Game $data
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

        $normalized['host'] = $this->normalizeHost($data);
        $normalized['gameMaster'] = $this->normalizeGameMaster($data);

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return
            $data instanceof Game
            && !(isset($context[self::ALREADY_CALLED]) && $context[self::ALREADY_CALLED])
            && isset($context['groups'])
            && (
                (\is_string($context['groups']) && 'game:read' === $context['groups'])
                || (\is_array($context['groups']) && \in_array('game:read', $context['groups'], true))
            );
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Game::class => false];
    }

    /** @return mixed[] */
    private function normalizeHost(Game $data): array
    {
        $host = $data->getHost();

        return ['id' => $host->getId()];
    }

    /** @return mixed[]|null */
    private function normalizeGameMaster(Game $data): ?array
    {
        $gameMaster = $data->getGameMaster();
        if (null === $gameMaster) {
            return null;
        }

        return ['id' => $gameMaster->getId(), 'username' => $gameMaster->getUsername()];
    }
}
