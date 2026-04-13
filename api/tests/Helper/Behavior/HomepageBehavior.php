<?php

namespace App\Tests\Helper\Behavior;

class HomepageBehavior extends AbstractBehavior
{
    public function get(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('GET', '/api/homepage'));
    }
}
