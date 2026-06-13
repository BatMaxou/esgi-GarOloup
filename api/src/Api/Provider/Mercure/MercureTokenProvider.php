<?php

namespace App\Api\Provider\Mercure;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Model\Mercure\MercureToken;
use App\Enum\Game\GameTeamEnum;
use App\Enum\TopicEnum;
use App\Repository\Game\PlayerRepository;
use App\Service\Mercure\TopicProvider;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/** @implements ProviderInterface<MercureToken> */
class MercureTokenProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly PlayerRepository $playerRepository,
        private readonly TopicProvider $topicProvider,
        private readonly HubInterface $hub,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): MercureToken
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof UserInterface) {
            return new MercureToken();
        }

        $factory = $this->hub->getFactory();
        if (null === $factory) {
            return new MercureToken();
        }

        $player = $this->playerRepository->findCurrentByUser($currentUser);
        $game = $player?->getGame();

        $isWerewolf = GameTeamEnum::WEREWOLF === $player?->getTeam();

        $token = $factory->create(subscribe: [
            ...($player ? [(string) $this->topicProvider->provide(TopicEnum::CURRENT_PLAYER, $player)] : []),
            ...($game ? [(string) $this->topicProvider->provide(TopicEnum::CURRENT_GAME, $game)] : []),
            ...($game && $isWerewolf ? [(string) $this->topicProvider->provide(TopicEnum::WEREWOLF_TEAM, $game)] : []),
        ], publish: []);

        return new MercureToken($token);
    }
}
