<?php

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Model\Homepage;
use App\Enum\Game\GameStepEnum;
use App\Repository\Game\GameRepository;
use App\Repository\RoleRepository;

/** @implements ProviderInterface<Homepage> */
class HomepageProvider implements ProviderInterface
{
    public function __construct(
        private readonly RoleRepository $roleRepository,
        private readonly GameRepository $gameRepository,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Homepage
    {
        $gameCriterias = ['public' => true, 'step' => GameStepEnum::NEW];
        $gameOrderBy = ['createdAt' => 'DESC'];
        $games = $this->gameRepository->findBy($gameCriterias, $gameOrderBy);

        $roleCriterias = [];
        $roleOrderBy = ['createdAt' => 'DESC'];
        $roles = $this->roleRepository->findBy($roleCriterias, $roleOrderBy);

        return new Homepage($roles, $games);
    }
}
