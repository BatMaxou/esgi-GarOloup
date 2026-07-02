<?php

namespace App\Api\Serializer\Normalizer\Game\Period;

use App\Entity\Game\Period\Day;
use App\Entity\User\AbstractUser;
use App\Repository\Game\PlayerRepository;
use App\Service\Game\Period\ActionVisibilityResolver;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DayActionsNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'day_actions_normalizer_already_called';

    public function __construct(
        private readonly Security $security,
        private readonly PlayerRepository $playerRepository,
        private readonly ActionVisibilityResolver $visibilityResolver,
    ) {
    }

    /** @param Day $object */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $context[self::ALREADY_CALLED] = true;
        $normalized = $this->normalizer->normalize($object, $format, $context);

        if (!\is_array($normalized)) {
            throw new \LogicException(\sprintf('Normalized data should be an array for %s', Day::class));
        }

        $normalizedActions = $normalized['actions'] ?? null;
        if (!\is_array($normalizedActions)) {
            return $normalized;
        }

        $currentPlayer = null;
        $currentUser = $this->security->getUser();
        if ($currentUser instanceof AbstractUser) {
            $currentPlayer = $this->playerRepository->findCurrentByUser($currentUser);
        }

        $actions = $object->getActions();

        $visibleActions = [];
        foreach ($actions as $index => $action) {
            if ($this->visibilityResolver->isMasked($action, $currentPlayer, $actions)) {
                continue;
            }

            if (\array_key_exists($index, $normalizedActions)) {
                $visibleActions[] = $normalizedActions[$index];
            }
        }

        $normalized['actions'] = $visibleActions;

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Day && false === ($context[self::ALREADY_CALLED] ?? false);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Day::class => false,
        ];
    }
}
