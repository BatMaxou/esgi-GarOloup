<?php

namespace App\Tests\Helper\Behavior\Security;

use App\Tests\Helper\Behavior\AbstractBehavior;
use App\Tests\Helper\Behavior\BehaviorResponse;

class AuthBehavior extends AbstractBehavior
{
    public function login(?string $email, ?string $password): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('POST', '/api/login', [
            'json' => [
                'username' => $email,
                'password' => $password,
            ],
        ]));
    }

    public function refresh(?string $refreshToken): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('POST', '/api/token/refresh', [
            'json' => [
                'refresh_token' => $refreshToken,
            ],
        ]));
    }
}
