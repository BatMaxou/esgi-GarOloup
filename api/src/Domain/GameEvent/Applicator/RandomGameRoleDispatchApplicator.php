<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\GameRoleNotFoundException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameRoleSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\SetGameConfigurationEvent;
use App\Entity\Event\Game\SetGameMasterEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Enum\Game\GameStepEnum;
use Doctrine\ORM\EntityManagerInterface;

/** @implements GameEventApplicatorInterface<SetGameConfigurationEvent> */
class RandomGameRoleDispatchApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly GameRoleSpec $gameRoleSpec,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        /** @var Player[] $players */
        $players = $game->getPlayers()->toArray();
        $roles = $game->getConfiguration()->getComposition()?->getRoles() ?? [];

        foreach ($roles as $roleEntry) {
            $role = $roleEntry->getRole();
            $count = $roleEntry->getCount();

            for ($i = 0; $i < $count; ++$i) {
                $index = \array_rand($players);
                $player = $players[$index];
                unset($players[$index]);

                if (!$gameRole = $this->gameRoleSpec->getAssociatedGameRole($role)) {
                    throw new GameRoleNotFoundException('Role not supported yet');
                }

                $this->em->persist($gameRole);
                $player->setRole($gameRole);
            }
        }

        return $game->setStep(GameStepEnum::READY);
    }

    public function supports(GameEvent $gameEvent): bool
    {
        $game = $gameEvent->getGame();
        if (
            !$game
            || (!$gameEvent instanceof SetGameConfigurationEvent && !$gameEvent instanceof SetGameMasterEvent)
            || GameStepEnum::DISPATCH !== $game->getStep()
            || !$game->getConfiguration()->isWithRandomDispatch()
        ) {
            return false;
        }

        return true;
    }

    public static function getPriority(): int
    {
        return static::POST_APPLY_PRIORITY;
    }
}
