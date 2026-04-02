<?php

namespace App\Tests\Functional\Game;

use App\Enum\Game\GameTeamEnum;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\When;

class GetGameTeamFiltersTest extends GarOloupApiTestCase
{
    public function test_can_retrieve_game_team_filters(): void
    {
        $response = When::filter()->getGameTeamFilters();
        $this->assertResponseStatusCodeSame(200);

        $filters = $response->get('[member]');
        $this->assertIsArray($filters);
        $this->assertContains(GameTeamEnum::VILLAGE->value, $filters);
        $this->assertContains(GameTeamEnum::WEREWOLF->value, $filters);
        $this->assertContains(GameTeamEnum::SOLO->value, $filters);
    }
}
