<?php

namespace App\Tests\Helper;

use App\Tests\Helper\Builder\Token\RefreshTokenBuilder;
use App\Tests\Helper\Builder\User\TempUserBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;

final class ThereIs
{
    public static function anUser(): UserBuilder
    {
        return new UserBuilder();
    }

    public static function aRefreshToken(): RefreshTokenBuilder
    {
        return new RefreshTokenBuilder();
    }

    public static function aTempUser(): TempUserBuilder
    {
        return new TempUserBuilder();
    }
}
