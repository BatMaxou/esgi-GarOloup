<?php

namespace App\Tests\Helper\Behavior\Filter;

use App\Tests\Helper\Behavior\AbstractBehavior;
use App\Tests\Helper\Behavior\BehaviorResponse;

class FilterBehavior extends AbstractBehavior
{
    public function getGameTeamFilters(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('GET', '/api/filters/game_teams'));
    }
}
