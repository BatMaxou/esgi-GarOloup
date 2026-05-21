<?php

namespace App\Fixtures\Story\Game;

use App\Enum\Game\GameInitialisationStepEnum;
use App\Fixtures\Story\GaroloupStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;

class GameCreatedStory extends GaroloupStory
{
    public const HOST = 'host';
    public const HOST_PLAYER = 'host_player';
    public const GAME = 'game';

    public const PLAYERS_POOL = 'players';

    public function build(): void
    {
        $hostBuilder = ThereIs::anUser()
            ->withUsername(\sprintf('%sslipman-game-host', $this->getPrefix()))
            ->withEmail(\sprintf('%sgame-host@garoloup.com', $this->getPrefix()));
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder);
        $this->addState(self::HOST, $hostBuilder);
        $this->addState(self::HOST_PLAYER, $hostPlayerBuilder);
        $this->addToPool(self::PLAYERS_POOL, $hostPlayerBuilder);

        $gameBuilder = ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withInitialisationStep(GameInitialisationStepEnum::NEW);
        $this->addState(self::GAME, $gameBuilder);
    }

    public function execute(): void
    {
        $hostBuilder = $this->getState(self::HOST);
        \assert($hostBuilder instanceof UserBuilder);
        $hostBuilder->build();

        $hostPlayerBuilder = $this->getState(self::HOST_PLAYER);
        \assert($hostPlayerBuilder instanceof PlayerBuilder);
        $hostPlayerBuilder->build();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $gameBuilder->build();
    }
}
