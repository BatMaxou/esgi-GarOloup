<?php

namespace App\Domain\GameEvent\Applicator;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\InvalidConfigurationException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\ConfigurationSpec;
use App\Domain\Spec\GameSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\SetConfigurationEvent;
use App\Entity\Game\Composition;
use App\Entity\Game\Game;
use App\Entity\Game\RoleEntry;
use App\Enum\Game\GameStepEnum;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;

/** @implements GameEventApplicatorInterface<SetConfigurationEvent> */
class SetConfigurationEventApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly GameSpec $gameSpec,
        private readonly ConfigurationSpec $configurationSpec,
        private readonly EntityManagerInterface $em,
        private readonly RoleRepository $roleRepository,
    ) {
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $user = $this->ensureUser($gameEvent);
        $game = $this->ensureGame($gameEvent);

        if (!$this->gameSpec->canSetConfiguration($user, $game)) {
            throw new UnauthorizedGameActionException('You can not set the configuration of this game');
        }

        $configuration = $game->getConfiguration();

        $composition = null;
        $compositionInput = $gameEvent->getComposition();
        if (null !== $compositionInput) {
            $composition = $configuration->getComposition() ?? new Composition();
            $composition->clearRoles();

            foreach ($compositionInput->roles as $roleEntryInput) {
                $role = $this->roleRepository->findOneBy(['type' => $roleEntryInput->role]);
                if (null === $role) {
                    continue;
                }

                $entry = new RoleEntry($composition, $role, $roleEntryInput->count);

                $composition->addRole($entry);
            }
        }

        $configuration->setComposition($composition);
        $configuration->setWithGameMaster($gameEvent->isWithGameMaster());
        $configuration->setWithRandomDispatch($gameEvent->isWithRandomDispatch());

        $game->setConfiguration($configuration);

        if (!$this->configurationSpec->isValid($game)) {
            throw new InvalidConfigurationException('Configuration is not valid');
        }

        // TODO: Move to GameSpec generic method
        $nextStep = $gameEvent->isWithGameMaster()
            ? GameStepEnum::GAME_MASTER_CHOICE
            : GameStepEnum::DISPATCH;

        $game->setStep($nextStep);

        $this->em->flush();

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof SetConfigurationEvent;
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }
}
