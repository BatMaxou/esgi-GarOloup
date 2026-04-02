<?php

namespace App\Api\Provider\Filter;

use App\Enum\Game\GameTeamEnum;

/** @extends FiltersProvider<GameTeamEnum> */
class GameTeamFiltersProvider extends FiltersProvider
{
    public function getClass(): string
    {
        return GameTeamEnum::class;
    }
}
