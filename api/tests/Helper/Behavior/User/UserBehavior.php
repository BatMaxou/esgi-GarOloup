<?php

namespace App\Tests\Helper\Behavior\User;

use App\Tests\Helper\Behavior\AbstractBehavior;
use App\Tests\Helper\Behavior\BehaviorResponse;

class UserBehavior extends AbstractBehavior
{
    public function register(?string $email, ?string $username, ?string $password): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('POST', '/api/register', [
            'json' => [
                'email' => $email,
                'username' => $username,
                'password' => $password,
            ],
        ]));
    }

    public function forgotPassword(?string $email): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('POST', '/api/forgot-password', [
            'json' => [
                'email' => $email,
            ],
        ]));
    }

    public function resetPassword(?string $token, ?string $password): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('POST', '/api/reset-password', [
            'json' => [
                'token' => $token,
                'password' => $password,
            ],
        ]));
    }
}
