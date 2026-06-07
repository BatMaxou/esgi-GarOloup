<?php

namespace App\Domain\Spec;

use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\Game\Role\SeerRole;
use App\Entity\Game\Role\VillagerRole;
use App\Entity\Game\Role\WerewolfRole;
use App\Entity\User\AbstractUser;
use App\Entity\User\TempUser;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Repository\Game\PlayerRepository;
use Symfony\Component\Clock\ClockInterface;

class GameSpec
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly ClockInterface $clock,
        private readonly int $minimumPlayers,
    ) {
    }

    public function canCreate(AbstractUser $user): bool
    {
        $players = $this->playerRepository->findByUser($user);
        if (0 === \count($players)) {
            return true;
        }

        foreach ($players as $player) {
            if (!$player->isDead()) {
                return false;
            }
        }

        return true;
    }

    public function canJoin(AbstractUser $user, Game $game): bool
    {
        $players = $this->playerRepository->findByUser($user);
        if (0 === \count($players)) {
            return true;
        }

        foreach ($players as $player) {
            if (!$player->isDead()) {
                return false;
            }
        }

        return $game->getMaxPlayers() > $game->getPlayers()->count();
    }

    public function isUsernameAvailable(TempUser $user, Game $game): bool
    {
        $requestedUsername = $user->getUsername();
        foreach ($game->getPlayers() as $player) {
            if ($player->getLinkedUser() === $user) {
                continue;
            }

            if ($requestedUsername === $player->getLinkedUser()->getUsername()) {
                return false;
            }
        }

        return true;
    }

    public function canCloseGameInvitation(AbstractUser $user, Game $game): bool
    {
        if ($user !== $game->getHost()->getLinkedUser()) {
            return false;
        }

        if ($game->getPlayers()->count() < $this->minimumPlayers) {
            return false;
        }

        return GameInitialisationStepEnum::NEW === $game->getInitialisationStep();
    }

    public function canLeave(AbstractUser $user, Game $game): bool
    {
        if (GameInitialisationStepEnum::NEW !== $game->getInitialisationStep()) {
            return false;
        }

        foreach ($game->getPlayers() as $player) {
            if ($player->getLinkedUser() === $user) {
                return true;
            }
        }

        return false;
    }

    public function canReOpenGameInvitation(AbstractUser $user, Game $game): bool
    {
        if ($user !== $game->getHost()->getLinkedUser()) {
            return false;
        }

        return GameInitialisationStepEnum::CONFIGURATION === $game->getInitialisationStep();
    }

    public function canSetConfiguration(AbstractUser $user, Game $game): bool
    {
        if ($user !== $game->getHost()->getLinkedUser()) {
            return false;
        }

        return GameInitialisationStepEnum::CONFIGURATION === $game->getInitialisationStep();
    }

    public function canResetConfiguration(AbstractUser $user, Game $game): bool
    {
        if ($user !== $game->getHost()->getLinkedUser() || null !== $game->getGameMaster()) {
            return false;
        }

        $step = $game->getInitialisationStep();

        return (
            GameInitialisationStepEnum::DISPATCH === $step
            || GameInitialisationStepEnum::GAME_MASTER_CHOICE === $step
        ) && !$game->getRuntimeStep();
    }

    public function canResetGameMaster(AbstractUser $user, Game $game): bool
    {
        if ($user !== $game->getHost()->getLinkedUser() || null === $game->getGameMaster()) {
            return false;
        }

        return GameInitialisationStepEnum::DISPATCH === $game->getInitialisationStep() && !$game->getRuntimeStep();
    }

    public function canSetGameMaster(AbstractUser $user, Game $game): bool
    {
        if ($user !== $game->getHost()->getLinkedUser() || !$game->getConfiguration()->isWithGameMaster()) {
            return false;
        }

        return GameInitialisationStepEnum::GAME_MASTER_CHOICE === $game->getInitialisationStep();
    }

    public function canLaunchGame(AbstractUser $user, Game $game): bool
    {
        if ($user !== $game->getHost()->getLinkedUser()) {
            return false;
        }

        return GameInitialisationStepEnum::FINISH === $game->getInitialisationStep() && !$game->getRuntimeStep();
    }

    public function canDispatchRoles(AbstractUser $user, Game $game): bool
    {
        if ($user !== $game->getGameMaster()?->getLinkedUser()) {
            return false;
        }

        return GameInitialisationStepEnum::DISPATCH === $game->getInitialisationStep();
    }

    public function canResetRoleDispatch(AbstractUser $user, Game $game): bool
    {
        if ($user !== $game->getGameMaster()?->getLinkedUser()) {
            return false;
        }

        if ($game->getConfiguration()->isWithRandomDispatch()) {
            return false;
        }

        return GameInitialisationStepEnum::FINISH === $game->getInitialisationStep() && !$game->getRuntimeStep();
    }

    public function canSeeWerewolfTeam(Player $player, Game $game): bool
    {
        return $game->getRuntimeStep() && $player->getRole() instanceof WerewolfRole;
    }

    public function areAllRolesSetup(Game $game): bool
    {
        foreach ($game->getPlayers() as $player) {
            if (!$player->getRole()?->isSetup()) {
                return false;
            }
        }

        return true;
    }

    public function canChooseFriend(Player $player, Game $game, string $targetPlayerId): bool
    {
        if (
            GameRuntimeStepEnum::SETUP !== $game->getRuntimeStep()
            || $game->getStepEndAt() < $this->clock->now()
            || $player->getId()?->toString() === $targetPlayerId
        ) {
            return false;
        }

        $targetExists = false;
        foreach ($game->getPlayers() as $player) {
            if ($player->getId()?->toString() === $targetPlayerId) {
                $targetExists = true;
            }
        }

        return $targetExists && $player->getRole() instanceof VillagerRole;
    }

    public function canWerewolfVote(Player $voter, Game $game, Player $targetPlayer): bool
    {
        if (GameRuntimeStepEnum::NIGHT !== $game->getRuntimeStep()) {
            return false;
        }

        if ($game->getStepEndAt() < $this->clock->now()) {
            return false;
        }

        $workflow = $game->getNightWorkflow();
        if (null === $workflow || !\in_array(GameRoleEnum::WEREWOLF, $workflow->getCurrentTurn(), true)) {
            return false;
        }

        if (!$voter->getRole() instanceof WerewolfRole || $voter->isDead()) {
            return false;
        }

        if ($voter->getId()?->toString() === $targetPlayer->getId()?->toString()) {
            return false;
        }

        if ($targetPlayer->isDead()) {
            return false;
        }

        if ($targetPlayer->getRole() instanceof WerewolfRole) {
            return false;
        }

        return true;
    }

    public function canSeerReveal(Player $seer, Game $game, Player $target): bool
    {
        if (GameRuntimeStepEnum::NIGHT !== $game->getRuntimeStep()) {
            return false;
        }

        if ($game->getStepEndAt() < $this->clock->now()) {
            return false;
        }

        $workflow = $game->getNightWorkflow();
        if (null === $workflow || !\in_array(GameRoleEnum::SEER, $workflow->getCurrentTurn(), true)) {
            return false;
        }

        $role = $seer->getRole();
        if (!$role instanceof SeerRole || $seer->isDead()) {
            return false;
        }

        if (null !== $role->getLastObservedPlayerId()) {
            return false;
        }

        if ($seer->getId()?->toString() === $target->getId()?->toString()) {
            return false;
        }

        if ($target->getGame() !== $game || $target->isDead()) {
            return false;
        }

        return true;
    }

    public function canVote(Player $voter, Game $game, Player $target): bool
    {
        if (GameRuntimeStepEnum::VOTE !== $game->getRuntimeStep()) {
            return false;
        }

        if ($game->getStepEndAt() < $this->clock->now()) {
            return false;
        }

        if ($voter->getGame() !== $game) {
            return false;
        }

        if ($voter->isDead()) {
            return false;
        }

        if ($voter->getId()?->toString() === $target->getId()?->toString()) {
            return false;
        }

        if ($target->isDead()) {
            return false;
        }

        return true;
    }
}
