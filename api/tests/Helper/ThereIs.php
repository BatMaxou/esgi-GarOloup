<?php

namespace App\Tests\Helper;

use App\Tests\Helper\Builder\User\UserBuilder;

class ThereIs
{
    public static function anUser(): UserBuilder
    {
        return new UserBuilder();
    }
}
