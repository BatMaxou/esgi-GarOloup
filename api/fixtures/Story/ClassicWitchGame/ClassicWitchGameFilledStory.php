<?php

namespace App\Fixtures\Story\ClassicWitchGame;

use App\Fixtures\Story\ClassicGame\ClassicGameFilledStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\ThereIs;

class ClassicWitchGameFilledStory extends ClassicGameFilledStory
{
    public const USER_7 = 'user_7';
    public const PLAYER_7 = 'player_7';

    public const USER_8 = 'user_8';
    public const PLAYER_8 = 'player_8';

    public function build(): void
    {
        parent::build();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $user7Builder = ThereIs::anUser()
            ->withUsername(\sprintf('%sslipman-7', $this->getPrefix()))
            ->withEmail(\sprintf('%sslipman-7@garoloup.com', $this->getPrefix()));
        $player7Builder = ThereIs::aPlayer()->withUser($user7Builder);
        $this->addState(self::USER_7, $user7Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_7, $player7Builder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $player7Builder);

        $user8Builder = ThereIs::anUser()
            ->withUsername(\sprintf('%sslipgirl-8', $this->getPrefix()))
            ->withEmail(\sprintf('%sslipgirl-8@garoloup.com', $this->getPrefix()));
        $player8Builder = ThereIs::aPlayer()->withUser($user8Builder);
        $this->addState(self::USER_8, $user8Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_8, $player8Builder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $player8Builder);

        $gameBuilder->withPlayer($player7Builder);
        $gameBuilder->withPlayer($player8Builder);
    }

    public function getPrefix(): string
    {
        return 'classic-witch-game-filled-';
    }
}
