<?php

namespace App\Tests\Helper\Behavior\Mercure;

use App\Tests\Helper\Behavior\AbstractBehavior;
use App\Tests\Helper\Behavior\BehaviorResponse;

class MercureBehavior extends AbstractBehavior
{
    public function getToken(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('GET', '/api/mercure/token'));
    }
}
