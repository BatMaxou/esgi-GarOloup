<?php

namespace App\Fixtures\Factory\User;

use App\Entity\User\User;

/** @extends AbstractUserFactory<User> */
class UserFactory extends AbstractUserFactory
{
    public static function class(): string
    {
        return User::class;
    }
}
