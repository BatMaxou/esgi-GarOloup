<?php

namespace App\Api\Serializer\Normalizer\Game\Period;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\DayAction;
use App\Entity\Game\Period\Action\NightAction;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GameRecapActionsNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public const GROUP = 'game:recap:read';

    private const ALREADY_CALLED = 'game_recap_actions_normalizer_already_called';

    /** @param Game $object */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $context[self::ALREADY_CALLED] = true;
        $normalized = $this->normalizer->normalize($object, $format, $context);

        if (!\is_array($normalized)) {
            throw new \LogicException(\sprintf('Normalized data should be an array for %s', Game::class));
        }

        /** @var array<int, NightAction|DayAction> $actions */
        $actions = [];
        foreach ($object->getNights() as $night) {
            foreach ($night->getActions() as $action) {
                $actions[] = $action;
            }
        }

        foreach ($object->getDays() as $day) {
            foreach ($day->getActions() as $action) {
                $actions[] = $action;
            }
        }

        \usort($actions, static fn (NightAction|DayAction $a, NightAction|DayAction $b): int => $a->getCreatedAt() <=> $b->getCreatedAt());

        $normalized['actions'] = \array_map(
            fn (NightAction|DayAction $action) => $this->normalizer->normalize($action, $format, $context),
            $actions,
        );

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Game || true === ($context[self::ALREADY_CALLED] ?? false)) {
            return false;
        }

        $groups = $context['groups'] ?? [];
        $groups = \is_array($groups) ? $groups : [$groups];

        return \in_array(self::GROUP, $groups, true);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Game::class => false,
        ];
    }
}
