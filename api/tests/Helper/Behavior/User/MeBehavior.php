<?php

namespace App\Tests\Helper\Behavior\User;

use App\Tests\Helper\Behavior\AbstractBehavior;
use App\Tests\Helper\Behavior\BehaviorResponse;

class MeBehavior extends AbstractBehavior
{
    public function get(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('GET', '/api/me'));
    }
}
