<?php

namespace App\Fixtures\Story\ClassicGame\GameMaster;

use App\Fixtures\Story\ClassicGame\ClassicGameFilledStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\ThereIs;

class ClassicGameFilledWithGameMasterStory extends ClassicGameFilledStory
{
    public const USER_6 = 'user_6';

    public const PLAYER_7 = 'player_7';

    public function build(): void
    {
        parent::build();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $user6Builder = ThereIs::anUser()
            ->withUsername(\sprintf('%sgame-master', $this->getPrefix()))
            ->withEmail(\sprintf('%sgame-master@garoloup.com', $this->getPrefix()));
        $player6Builder = ThereIs::aPlayer()->withUser($user6Builder);
        $this->addState(self::USER_6, $user6Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_7, $player6Builder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $player6Builder);

        $gameBuilder->withPlayer($player6Builder);
    }

    public function getPrefix(): string
    {
        return 'classic-game-filled-with-game-master-';
    }
}
