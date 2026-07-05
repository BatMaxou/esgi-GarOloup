<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Role\InfectFather;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\Role\InfectFatherSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\InfectEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\NightAction\InfectAction;
use App\Entity\Game\Role\InfectFatherRole;
use App\Enum\Game\GameRoleEnum;
use App\Repository\Game\PlayerRepository;

/** @implements GameEventApplicatorInterface<InfectEvent> */
class InfectApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly InfectFatherSpec $infectFatherSpec,
        private readonly PlayerRepository $playerRepository,
    ) {
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $user = $this->ensureUser($gameEvent);
        $game = $this->ensureGame($gameEvent);

        $player = $this->playerRepository->findCurrentByUser($user);
        if (null === $player) {
            throw new PlayerNotFoundException('Current player not found');
        }

        if (!$this->infectFatherSpec->canInfect($player, $game)) {
            throw new UnauthorizedGameActionException('You can not infect');
        }

        $victimId = $this->infectFatherSpec->findWerewolfVictimId($game);
        if (null === $victimId) {
            throw new UnauthorizedGameActionException('No werewolf victim to infect');
        }

        $night = $game->getCurrentNight() ?? throw new \LogicException('No active night to register the infection');
        $night->addAction(new InfectAction($night, GameRoleEnum::INFECT_FATHER, $victimId));

        $role = $player->getRoleAs(InfectFatherRole::class);
        if (!$role instanceof InfectFatherRole) {
            throw new \LogicException(\sprintf('Role must be verified as a %s here', InfectFatherRole::class));
        }

        $role->useInfection();

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof InfectEvent;
    }
}
