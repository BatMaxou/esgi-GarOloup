<?php

namespace App\Tests\Helper;

use App\Tests\Helper\Builder\File\UploadFileBuilder;
use App\Tests\Helper\Builder\Game\CompositionBuilder;
use App\Tests\Helper\Builder\Game\ConfigurationBuilder;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\Role\RoleBuilder;
use App\Tests\Helper\Builder\Role\RoleBuilderBag;
use App\Tests\Helper\Builder\Security\RefreshTokenBuilder;
use App\Tests\Helper\Builder\User\AdminBuilder;
use App\Tests\Helper\Builder\User\TempUserBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;

final class ThereIs
{
    public static function anAdmin(): AdminBuilder
    {
        return new AdminBuilder();
    }

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

    public static function aPlayer(): PlayerBuilder
    {
        return new PlayerBuilder();
    }

    public static function aGame(): GameBuilder
    {
        return new GameBuilder();
    }

    public static function aComposition(): CompositionBuilder
    {
        return new CompositionBuilder();
    }

    public static function aConfiguration(): ConfigurationBuilder
    {
        return new ConfigurationBuilder();
    }

    public static function aRole(): RoleBuilder
    {
        return new RoleBuilder();
    }

    public static function aRoleBag(): RoleBuilderBag
    {
        return new RoleBuilderBag(static::aRole());
    }

    public static function anUploadFile(): UploadFileBuilder
    {
        return new UploadFileBuilder();
    }
}
