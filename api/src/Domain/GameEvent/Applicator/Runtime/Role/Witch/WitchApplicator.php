<?php

namespace App\Domain\GameEvent\Applicator\Runtime\Role\Witch;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\Role\WitchSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\WitchPoisonEvent;
use App\Entity\Event\Game\WitchSaveEvent;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\NightAction\MurderAction;
use App\Entity\Game\Period\Action\NightAction\SaveAction;
use App\Entity\Game\Role\WitchRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\TopicEnum;
use App\Repository\Game\PlayerRepository;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

/** @implements GameEventApplicatorInterface<WitchSaveEvent|WitchPoisonEvent> */
class WitchApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly WitchSpec $witchSpec,
        private readonly PlayerRepository $playerRepository,
        private readonly ClockInterface $clock,
        private readonly TopicProvider $topicProvider,
        private readonly TopicCollector $topicCollector,
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

        $targetPlayerId = $gameEvent->getTargetPlayerId();
        if (!Uuid::isValid($targetPlayerId)) {
            throw new PlayerNotFoundException('Target player not found');
        }

        $targetPlayer = $this->playerRepository->find($targetPlayerId);
        if (null === $targetPlayer || $game !== $targetPlayer->getGame()) {
            throw new PlayerNotFoundException('Target player not found');
        }

        $night = $game->getCurrentNight() ?? throw new \LogicException('No active night to register the witch action');

        $role = $player->getRoleAs(WitchRole::class);

        if ($gameEvent instanceof WitchSaveEvent) {
            if (!$this->witchSpec->canSave($player, $game, $targetPlayer)) {
                throw new UnauthorizedGameActionException('You can not save');
            }

            if (null === $role) {
                throw new \LogicException(\sprintf('Role must be verified as a %s here', WitchRole::class));
            }

            $night->addAction(new SaveAction($night, GameRoleEnum::WITCH, $targetPlayerId));
            $role->useHealPotion();

            $game->setStepEndAt($this->clock->now());
            $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_GAME, $game));

            return $game;
        }

        if (!$this->witchSpec->canPoison($player, $game, $targetPlayer)) {
            throw new UnauthorizedGameActionException('You can not poison');
        }

        if (null === $role) {
            throw new \LogicException(\sprintf('Role must be verified as a %s here', WitchRole::class));
        }

        $night->addAction(new MurderAction($night, GameRoleEnum::WITCH, $targetPlayerId));
        $role->usePoisonPotion();
        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_GAME, $game));

        $game->setStepEndAt($this->clock->now());

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof WitchSaveEvent || $gameEvent instanceof WitchPoisonEvent;
    }
}
