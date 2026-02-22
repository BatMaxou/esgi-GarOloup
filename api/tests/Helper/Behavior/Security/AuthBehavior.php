<?php

namespace App\Tests\Helper\Behavior\Security;

use App\Tests\Helper\Behavior\AbstractBehavior;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AuthBehavior extends AbstractBehavior
{
    public function login(?string $email, ?string $password): ResponseInterface
    {
        return $this->client->request('POST', '/api/login', [
            'json' => [
                'username' => $email,
                'password' => $password,
            ],
        ]);
    }
}
