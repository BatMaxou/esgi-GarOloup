<?php

namespace App\Fixtures\Story\ClassicGame\GameMaster;

use App\Fixtures\Story\ClassicGame\ClassicGameFilledStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\ThereIs;

class ClassicGameFilledWithGameMasterStory extends ClassicGameFilledStory
{
    public const TEMP_USER_6 = 'temp_user_5';

    public const PLAYER_7 = 'player_6';

    public function build(): void
    {
        parent::build();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $temp6Builder = ThereIs::aTempUser()->withUsername(\sprintf('%sgame-master', $this->getPrefix()));
        $temp6PlayerBuilder = ThereIs::aPlayer()->withTempUser($temp6Builder);
        $this->addState(self::TEMP_USER_6, $temp6Builder, self::TEMP_USERS_POOL);
        $this->addState(self::PLAYER_7, $temp6PlayerBuilder, self::TEMP_PLAYERS_POOL);
        $this->addToPool(self::PLAYERS_POOL, $temp6PlayerBuilder);

        $gameBuilder->withPlayer($temp6PlayerBuilder);
    }

    public function getPrefix(): string
    {
        return 'classic-game-filled-with-game-master-';
    }
}
