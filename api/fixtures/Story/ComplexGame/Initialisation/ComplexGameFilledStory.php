<?php

namespace App\Fixtures\Story\ComplexGame\Initialisation;

use App\Fixtures\Story\ClassicGame\Initialisation\ClassicGameFilledStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\ThereIs;

class ComplexGameFilledStory extends ClassicGameFilledStory
{
    public const USER_7 = 'user_7';
    public const PLAYER_7 = 'player_7';

    public const USER_8 = 'user_8';
    public const PLAYER_8 = 'player_8';

    public const USER_9 = 'user_9';
    public const PLAYER_9 = 'player_9';

    public const USER_10 = 'user_10';
    public const PLAYER_10 = 'player_10';

    public const USER_11 = 'user_11';
    public const PLAYER_11 = 'player_11';

    public const USER_12 = 'user_12';
    public const PLAYER_12 = 'player_12';

    public function build(): void
    {
        parent::build();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        foreach (\range(7, 12) as $index) {
            $userBuilder = ThereIs::anUser()
                ->withUsername(\sprintf('%sslipman-%d', $this->getPrefix(), $index))
                ->withEmail(\sprintf('%sslipman-%d@garoloup.com', $this->getPrefix(), $index));
            $playerBuilder = ThereIs::aPlayer()->withUser($userBuilder);

            $this->addState(\sprintf('user_%d', $index), $userBuilder, self::TEMP_USERS_POOL);
            $this->addState(\sprintf('player_%d', $index), $playerBuilder, self::TEMP_PLAYERS_POOL);
            $this->addToPool(self::PLAYERS_POOL, $playerBuilder);

            $gameBuilder->withPlayer($playerBuilder);
        }
    }

    public function getPrefix(): string
    {
        return 'complex-game-filled-';
    }
}
