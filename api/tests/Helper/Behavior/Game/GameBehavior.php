<?php

namespace App\Tests\Helper\Behavior\Game;

use App\Tests\Helper\Behavior\AbstractBehavior;
use App\Tests\Helper\Behavior\BehaviorResponse;

class GameBehavior extends AbstractBehavior
{
    public function create(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('POST', '/api/games', [
            'json' => [],
        ]));
    }
}
