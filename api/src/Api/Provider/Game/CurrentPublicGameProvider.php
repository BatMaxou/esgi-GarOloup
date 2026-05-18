<?php

namespace App\Api\Provider\Game;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Game\Game;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Repository\Game\GameRepository;

/** @implements ProviderInterface<Game> */
class CurrentPublicGameProvider implements ProviderInterface
{
    public function __construct(
        private readonly GameRepository $gameRepository,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): TraversablePaginator
    {
        $filters = $context['filters'] ?? [];
        if (!\is_array($filters)) {
            throw new \InvalidArgumentException('The filters must be an array.');
        }

        $page = $filters['page'] ?? 1;
        if (\is_string($page)) {
            $page = (int) $page;
        }

        if (!\is_int($page) || $page < 1) {
            throw new \InvalidArgumentException('The page must be a positive integer.');
        }

        $itemsPerPage = $filters['itemsPerPage'] ?? $operation->getPaginationItemsPerPage() ?? 10;
        if (\is_string($itemsPerPage)) {
            $itemsPerPage = (int) $itemsPerPage;
        }

        if (!\is_int($itemsPerPage) || $itemsPerPage < 1) {
            throw new \InvalidArgumentException('The itemsPerPage must be a positive integer.');
        }

        $offset = ($page - 1) * $itemsPerPage;
        $criterias = ['public' => true, 'initialisationStep' => GameInitialisationStepEnum::NEW];
        $orderBy = ['createdAt' => 'DESC'];

        $games = $this->gameRepository->findBy($criterias, $orderBy, $itemsPerPage, $offset);
        $totalItems = $this->gameRepository->count($criterias);

        return new TraversablePaginator(new \ArrayIterator($games), $page, $itemsPerPage, $totalItems);
    }
}
