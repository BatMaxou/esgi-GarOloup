<?php

namespace App\Fixtures\Story\ClassicGame;

use App\Fixtures\Story\Game\GameCreatedStory;
use App\Tests\Helper\Builder\AbstractBuilder;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ClassicGameFilledStory extends GameCreatedStory
{
    public const USER_1 = 'user_1';
    public const USER_2 = 'user_2';
    public const USER_3 = 'user_3';
    public const USER_4 = 'user_4';
    public const USER_5 = 'user_5';

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

        $user1Builder = ThereIs::anUser()
            ->withUsername(\sprintf('%sslipman-1', $this->getPrefix()))
            ->withEmail(\sprintf('%sslipman-1@garoloup.com', $this->getPrefix()));
        $player1Builder = ThereIs::aPlayer()->withUser($user1Builder);
        $this->addState(self::USER_1, $user1Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_1, $player1Builder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $player1Builder);

        $user2Builder = ThereIs::anUser()
            ->withUsername(\sprintf('%sslipgirl-2', $this->getPrefix()))
            ->withEmail(\sprintf('%sslipgirl-2@garoloup.com', $this->getPrefix()));
        $player2Builder = ThereIs::aPlayer()->withUser($user2Builder);
        $this->addState(self::USER_2, $user2Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_2, $player2Builder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $player2Builder);

        $user3Builder = ThereIs::anUser()
            ->withUsername(\sprintf('%sslipman-3', $this->getPrefix()))
            ->withEmail(\sprintf('%sslipman-3@garoloup.com', $this->getPrefix()));
        $player3Builder = ThereIs::aPlayer()->withUser($user3Builder);
        $this->addState(self::USER_3, $user3Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_3, $player3Builder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $player3Builder);

        $user4Builder = ThereIs::anUser()
            ->withUsername(\sprintf('%sslipgirl-4', $this->getPrefix()))
            ->withEmail(\sprintf('%sslipgirl-4@garoloup.com', $this->getPrefix()));
        $player4Builder = ThereIs::aPlayer()->withUser($user4Builder);
        $this->addState(self::USER_4, $user4Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_4, $player4Builder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $player4Builder);

        $user5Builder = ThereIs::anUser()
            ->withUsername(\sprintf('%sslipman-5', $this->getPrefix()))
            ->withEmail(\sprintf('%sslipman-5@garoloup.com', $this->getPrefix()));
        $player5Builder = ThereIs::aPlayer()->withUser($user5Builder);
        $this->addState(self::USER_5, $user5Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_5, $player5Builder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $player5Builder);

        $gameBuilder->withPlayers([
            $player1Builder,
            $player2Builder,
            $player3Builder,
            $player4Builder,
            $player5Builder,
        ]);
    }

    public function execute(): void
    {
        foreach ($this->getPool(self::TEMP_USERS_POOL) as $userBuilder) {
            \assert($userBuilder instanceof AbstractBuilder);
            $userBuilder->build();
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
