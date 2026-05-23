<?php

namespace App\Fixtures\Story\ClassicGame;

use App\Fixtures\Story\Game\GameCreatedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\TempUserBuilder;
use App\Tests\Helper\ThereIs;

class ClassicGameFilledStory extends GameCreatedStory
{
    public const TEMP_USER_1 = 'temp_user_1';
    public const TEMP_USER_2 = 'temp_user_2';
    public const TEMP_USER_3 = 'temp_user_3';
    public const TEMP_USER_4 = 'temp_user_4';
    public const TEMP_USER_5 = 'temp_user_5';

    public const PLAYER_1 = 'player_1';
    public const PLAYER_2 = 'player_2';
    public const PLAYER_3 = 'player_3';
    public const PLAYER_4 = 'player_4';
    public const PLAYER_5 = 'player_5';
    public const PLAYER_6 = 'player_6';

    public const TEMP_USERS_POOL = 'temp_users';
    public const TEMP_PLAYERS_POOL = 'temp_players';

    public function build(): void
    {
        parent::build();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $hostPlayerBuilder = $this->getState(self::HOST_PLAYER);
        \assert($hostPlayerBuilder instanceof PlayerBuilder);
        $this->addState(self::PLAYER_6, $hostPlayerBuilder);

        $temp1Builder = ThereIs::aTempUser()->withUsername(\sprintf('%sslipman-temp-1', $this->getPrefix()));
        $temp1PlayerBuilder = ThereIs::aPlayer()->withTempUser($temp1Builder);
        $this->addState(self::TEMP_USER_1, $temp1Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_1, $temp1PlayerBuilder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $temp1PlayerBuilder);

        $temp2Builder = ThereIs::aTempUser()->withUsername(\sprintf('%sslipgirl-temp-2', $this->getPrefix()));
        $temp2PlayerBuilder = ThereIs::aPlayer()->withTempUser($temp2Builder);
        $this->addState(self::TEMP_USER_2, $temp2Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_2, $temp2PlayerBuilder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $temp2PlayerBuilder);

        $temp3Builder = ThereIs::aTempUser()->withUsername(\sprintf('%sslipman-temp-3', $this->getPrefix()));
        $temp3PlayerBuilder = ThereIs::aPlayer()->withTempUser($temp3Builder);
        $this->addState(self::TEMP_USER_3, $temp3Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_3, $temp3PlayerBuilder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $temp3PlayerBuilder);

        $temp4Builder = ThereIs::aTempUser()->withUsername(\sprintf('%sslipgirl-temp-4', $this->getPrefix()));
        $temp4PlayerBuilder = ThereIs::aPlayer()->withTempUser($temp4Builder);
        $this->addState(self::TEMP_USER_4, $temp4Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_4, $temp4PlayerBuilder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $temp4PlayerBuilder);

        $temp5Builder = ThereIs::aTempUser()->withUsername(\sprintf('%sslipman-temp-5', $this->getPrefix()));
        $temp5PlayerBuilder = ThereIs::aPlayer()->withTempUser($temp5Builder);
        $this->addState(self::TEMP_USER_5, $temp5Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_5, $temp5PlayerBuilder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $temp5PlayerBuilder);

        $gameBuilder->withPlayers([
            $temp1PlayerBuilder,
            $temp2PlayerBuilder,
            $temp3PlayerBuilder,
            $temp4PlayerBuilder,
            $temp5PlayerBuilder,
        ]);
    }

    public function execute(): void
    {
        foreach ($this->getPool(self::TEMP_USERS_POOL) as $tempUserBuilder) {
            \assert($tempUserBuilder instanceof TempUserBuilder);
            $tempUserBuilder->build();
        }

        foreach ($this->getPool(self::TEMP_PLAYERS_POOL) as $tempPlayerBuilder) {
            \assert($tempPlayerBuilder instanceof PlayerBuilder);
            $tempPlayerBuilder->build();
        }

        parent::execute();
    }

    public function getPrefix(): string
    {
        return 'classic-game-filled-';
    }
}
