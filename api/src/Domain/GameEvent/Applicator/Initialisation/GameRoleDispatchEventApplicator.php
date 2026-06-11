<?php

namespace App\Domain\GameEvent\Applicator\Initialisation;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\InvalidRoleDispatchException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\GameRoleSpec;
use App\Domain\Spec\GameSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\GameRoleDispatchEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\Role;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Repository\Game\PlayerRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;

/** @implements GameEventApplicatorInterface<GameRoleDispatchEvent> */
class GameRoleDispatchEventApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly GameSpec $gameSpec,
        private readonly GameRoleSpec $gameRoleSpec,
        private readonly PlayerRepository $playerRepository,
        private readonly RoleRepository $roleRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $user = $this->ensureUser($gameEvent);
        $game = $this->ensureGame($gameEvent);

        if (!$this->gameSpec->canDispatchRoles($user, $game)) {
            throw new UnauthorizedGameActionException('You can not dispatch roles for this game');
        }

        $dispatch = $gameEvent->getDispatch();
        if ($game->getPlayers()->count() !== \count($dispatch)) {
            throw new InvalidRoleDispatchException('The number of role dispatch entries must match the number of players');
        }

        $composition = $game->getConfiguration()->getComposition();
        if (null === $composition) {
            throw new \LogicException('The game configuration is not set');
        }

        $remainingSlots = [];
        foreach ($composition->getRoles() as $roleEntry) {
            $type = $roleEntry->getRole()->getType();
            if (null !== $type) {
                $remainingSlots[$type->value] = $roleEntry->getCount();
            }
        }

        foreach ($dispatch as $entry) {
            [$player, $role] = $this->ensureValidEntry(
                $game,
                $this->playerRepository->find($entry->playerId),
                $this->roleRepository->findOneBy(['type' => $entry->role]),
                $remainingSlots
            );

            $gameRole = $this->gameRoleSpec->getAssociatedGameRole($role);
            if (null === $gameRole) {
                throw new InvalidRoleDispatchException('Invalid role');
            }

            $this->em->persist($gameRole);
            $player->setRole($gameRole);
        }

        if (\array_sum($remainingSlots) > 0) {
            throw new InvalidRoleDispatchException('All roles from the composition must be dispatched');
        }

        $game->setInitialisationStep(GameInitialisationStepEnum::FINISH);

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof GameRoleDispatchEvent;
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }

    /**
     * @param array<string, int> $remainingSlots
     *
     * @return array{0: Player, 1: Role}
     */
    private function ensureValidEntry(Game $game, ?Player $player, ?Role $role, array &$remainingSlots): array
    {
        if (null === $player || $game !== $player->getGame()) {
            throw new InvalidRoleDispatchException('Player not found');
        }

        if (null === $role) {
            throw new InvalidRoleDispatchException('Role not found');
        }

        $type = $role->getType();
        if (null === $type || !isset($remainingSlots[$type->value]) || $remainingSlots[$type->value] <= 0) {
            throw new InvalidRoleDispatchException('Role is not available in the composition');
        }

        --$remainingSlots[$type->value];

        return [$player, $role];
    }
}
