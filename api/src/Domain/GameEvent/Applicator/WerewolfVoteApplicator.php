<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\AfkAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\RandomizationAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\Role\WerewolfSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Event\Game\WerewolfVoteEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\NightAction\MurderAction;
use App\Entity\Game\Player;
use App\Entity\Game\Role\WerewolfRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Repository\Game\PlayerRepository;
use Symfony\Component\Uid\Uuid;

/** @implements GameEventApplicatorInterface<WerewolfVoteEvent|TimeUpGameEvent> */
class WerewolfVoteApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;
    use AfkAwareTrait;
    /** @use RandomizationAwareTrait<WerewolfVoteEvent, TimeUpGameEvent> */
    use RandomizationAwareTrait;

    public function __construct(
        private readonly WerewolfSpec $werewolfSpec,
        private readonly PlayerRepository $playerRepository,
        private readonly int $afkThreshold,
    ) {
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }

    protected function applyAction(GameEvent $gameEvent): Game
    {
        $user = $this->ensureUser($gameEvent);
        $game = $this->ensureGame($gameEvent);

        $player = $this->playerRepository->findCurrentByUser($user);
        if (null === $player) {
            throw new PlayerNotFoundException('Current player not found');
        }

        $targetPlayerId = $gameEvent->getTargetPlayerId();
        if (!Uuid::isValid($targetPlayerId)) {
            throw new PlayerNotFoundException('Target player not found');
        }

        $targetPlayer = $this->playerRepository->find($targetPlayerId);
        if (null === $targetPlayer || $game !== $targetPlayer->getGame()) {
            throw new PlayerNotFoundException('Target player not found');
        }

        if (!$this->werewolfSpec->canVote($player, $game, $targetPlayer)) {
            throw new UnauthorizedGameActionException('You can not vote');
        }

        $role = $player->getRole();
        if (!$role instanceof WerewolfRole) {
            throw new \LogicException(\sprintf('Role must be verified as a %s here', WerewolfRole::class));
        }

        $role->setTargetPlayerId($targetPlayerId);

        return $game;
    }

    protected function randomizeAction(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        foreach ($game->getPlayers() as $player) {
            $role = $player->getRole();
            if ($role instanceof WerewolfRole && !$player->isDead() && null === $role->getTargetPlayerId()) {
                $role->setTargetPlayerId($this->getRandomVictimId($game));
                $this->handleAfkPlayer($player);
            }
        }

        return $this->closeWerewolfVote($game);
    }

    protected function supportsRandomization(GameEvent $gameEvent): bool
    {
        $game = $gameEvent->getGame();
        if (!$gameEvent instanceof TimeUpGameEvent || null === $game) {
            return false;
        }

        if (GameRuntimeStepEnum::NIGHT !== $game->getRuntimeStep()) {
            return false;
        }

        $workflow = $game->getNightWorkflow();

        return null !== $workflow && \in_array(GameRoleEnum::WEREWOLF, $workflow->getCurrentTurn(), true);
    }

    protected function supportsAction(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof WerewolfVoteEvent;
    }

    protected function getAfkThreshold(): int
    {
        return $this->afkThreshold;
    }

    /**
     * @return Player[]
     */
    private function getAliveNonWerewolves(Game $game): array
    {
        $candidates = [];
        foreach ($game->getPlayers() as $player) {
            if ($player->isDead()) {
                continue;
            }
            if ($player->getRole() instanceof WerewolfRole) {
                continue;
            }
            $candidates[] = $player;
        }

        return $candidates;
    }

    private function getRandomVictimId(Game $game): string
    {
        $candidates = $this->getAliveNonWerewolves($game);
        if ([] === $candidates) {
            throw new \LogicException('No valid victim available');
        }

        $victim = $candidates[\array_rand($candidates)];
        $victimId = $victim->getId()?->toString();
        if (null === $victimId) {
            throw new \LogicException('Random victim id should not be null here');
        }

        return $victimId;
    }

    private function closeWerewolfVote(Game $game): Game
    {
        $victimId = $this->resolveVictim($game);
        if (null !== $victimId) {
            $night = $game->getCurrentNight() ?? throw new \LogicException('No active night to register the murder');
            $murder = new MurderAction($night, GameRoleEnum::WEREWOLF, $victimId);
            $night->addAction($murder);
        }

        return $game;
    }

    private function resolveVictim(Game $game): ?string
    {
        $tally = [];
        foreach ($game->getPlayers() as $player) {
            $role = $player->getRole();
            if (!$role instanceof WerewolfRole || $player->isDead()) {
                continue;
            }

            $targetId = $role->getTargetPlayerId();
            if (null === $targetId) {
                continue;
            }

            $tally[$targetId] = ($tally[$targetId] ?? 0) + 1;
        }

        if (empty($tally)) {
            return null;
        }

        $maxVotes = \max($tally);
        $topTargets = \array_keys(\array_filter($tally, fn (int $count) => $count === $maxVotes));

        return $topTargets[\array_rand($topTargets)];
    }
}
