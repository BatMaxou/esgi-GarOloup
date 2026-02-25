<?php

namespace App\Tests\Helper\Behavior\User;

use App\Tests\Helper\Behavior\AbstractBehavior;
use App\Tests\Helper\Behavior\BehaviorResponse;

class TempUserBehavior extends AbstractBehavior
{
    public function get(string $username): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('GET', '/api/temp_user', [
            'query' => [
                'username' => $username,
            ],
        ]));
    }
}
