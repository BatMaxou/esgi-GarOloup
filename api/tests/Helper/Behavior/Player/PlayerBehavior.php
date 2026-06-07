<?php

namespace App\Tests\Helper\Behavior\Player;

use App\Tests\Helper\Behavior\AbstractBehavior;
use App\Tests\Helper\Behavior\BehaviorResponse;

class PlayerBehavior extends AbstractBehavior
{
    public function getCurrent(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('GET', '/api/game/player'));
    }

    public function leave(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('DELETE', '/api/game/player', [
            'json' => [],
        ]));
    }
}
