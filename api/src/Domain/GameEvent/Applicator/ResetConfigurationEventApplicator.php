<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\ResetConfigurationEvent;
use App\Entity\Game\Game;
use App\Enum\Game\GameInitialisationStepEnum;
use Doctrine\ORM\EntityManagerInterface;

/** @implements GameEventApplicatorInterface<ResetConfigurationEvent> */
class ResetConfigurationEventApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly GameSpec $gameSpec,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $user = $this->ensureUser($gameEvent);
        $game = $this->ensureGame($gameEvent);

        if (!$this->gameSpec->canResetConfiguration($user, $game)) {
            throw new UnauthorizedGameActionException('You can not reset the configuration of this game');
        }

        $configuration = $game->getConfiguration();

        $composition = $configuration->getComposition();
        if (null !== $composition) {
            $configuration->setComposition(null);
            $this->em->remove($composition);
        }

        $configuration
            ->setWithGameMaster(false)
            ->setWithRandomDispatch(true);

        return $game->setInitialisationStep(GameInitialisationStepEnum::CONFIGURATION);
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof ResetConfigurationEvent;
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }
}
