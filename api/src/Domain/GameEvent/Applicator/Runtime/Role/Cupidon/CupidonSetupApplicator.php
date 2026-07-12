<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Role\Cupidon;

use App\Domain\GameEvent\Applicator\Trait\AfkAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\RandomizationAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\Role\CupidonSpec;
use App\Entity\Event\Game\CupidonSetupEvent;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\SetupAction\CoupleAction;
use App\Entity\Game\Player;
use App\Entity\Game\Role\CupidonRole;
use App\Entity\Game\Role\LoverRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\TopicEnum;
use App\Repository\Game\PlayerRepository;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;
use Doctrine\ORM\EntityManagerInterface;

/** @implements GameEventApplicatorInterface<CupidonSetupEvent|TimeUpGameEvent> */
class CupidonSetupApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;
    use AfkAwareTrait;
    /** @use RandomizationAwareTrait<CupidonSetupEvent, TimeUpGameEvent> */
    use RandomizationAwareTrait;

    public function __construct(
        private readonly CupidonSpec $cupidonSpec,
        private readonly PlayerRepository $playerRepository,
        private readonly TopicCollector $topicCollector,
        private readonly TopicProvider $topicProvider,
        private readonly EntityManagerInterface $em,
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

        $firstLoverId = $gameEvent->getFirstLoverId();
        $secondLoverId = $gameEvent->getSecondLoverId();

        if (!$this->cupidonSpec->canChooseLovers($player, $game, $firstLoverId, $secondLoverId)) {
            throw new UnauthorizedGameActionException('You can not choose lovers');
        }

        $role = $player->getRoleAs(CupidonRole::class);
        if (!$role instanceof CupidonRole) {
            throw new UnauthorizedGameActionException('You can not choose lovers');
        }

        $this->bindLovers($game, $firstLoverId, $secondLoverId);
        $role->setSetup(true);

        return $game;
    }

    protected function randomizeAction(GameEvent $gameEvent): Game
    {
        $game = $this->ensureGame($gameEvent);

        foreach ($game->getPlayers() as $player) {
            $role = $player->getRoleAs(CupidonRole::class);
            if ($role instanceof CupidonRole && !$role->isSetup()) {
                [$firstLoverId, $secondLoverId] = $this->getRandomLoverIds($game, $player);
                $this->bindLovers($game, $firstLoverId, $secondLoverId);
                $role->setSetup(true);
                $this->handleAfkPlayer($player);
            }
        }

        return $game;
    }

    protected function supportsRandomization(GameEvent $gameEvent): bool
    {
        $game = $gameEvent->getGame();

        return $gameEvent instanceof TimeUpGameEvent
            && $game
            && GameRuntimeStepEnum::SETUP === $game->getRuntimeStep()
            && $game->getPlayer(GameRoleEnum::CUPIDON)
        ;
    }

    protected function supportsAction(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof CupidonSetupEvent;
    }

    protected function getAfkThreshold(): int
    {
        return $this->afkThreshold;
    }

    private function bindLovers(Game $game, string $firstLoverId, string $secondLoverId): void
    {
        $first = $this->findPlayer($game, $firstLoverId);
        $second = $this->findPlayer($game, $secondLoverId);

        $this->wrapAsLover($first, $secondLoverId);
        $this->wrapAsLover($second, $firstLoverId);

        $setup = $game->getSetup();
        if (null !== $setup) {
            $setup->addAction(new CoupleAction($setup, $firstLoverId, $secondLoverId));
        }

        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_PLAYER, $first));
        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_PLAYER, $second));
    }

    private function findPlayer(Game $game, string $playerId): Player
    {
        foreach ($game->getPlayers() as $player) {
            if ($player->getId()?->toString() === $playerId) {
                return $player;
            }
        }

        throw new PlayerNotFoundException('Lover not found');
    }

    private function wrapAsLover(Player $player, string $partnerPlayerId): void
    {
        $originalRole = $player->getRole();
        if (null === $originalRole) {
            throw new \LogicException('Lover must have a role here');
        }

        $loverRole = new LoverRole($originalRole, $partnerPlayerId);
        $this->em->persist($loverRole);

        $player->setRole($loverRole);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function getRandomLoverIds(Game $game, Player $cupidon): array
    {
        $candidates = [];
        foreach ($game->getPlayers() as $player) {
            if ($player->getId()?->toString() !== $cupidon->getId()?->toString()) {
                $candidates[] = $player;
            }
        }

        if (\count($candidates) < 2) {
            throw new \LogicException('Not enough players to pick lovers');
        }

        $keys = \array_rand($candidates, 2);
        $firstLoverId = $candidates[$keys[0]]->getId()?->toString();
        $secondLoverId = $candidates[$keys[1]]->getId()?->toString();
        if (!$firstLoverId || !$secondLoverId) {
            throw new \LogicException('Random lover ids should not be null here');
        }

        return [$firstLoverId, $secondLoverId];
    }
}
