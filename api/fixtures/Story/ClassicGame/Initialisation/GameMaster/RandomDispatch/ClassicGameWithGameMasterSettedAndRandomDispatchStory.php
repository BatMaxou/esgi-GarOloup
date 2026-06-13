<?php

namespace App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\RandomDispatch;

use App\Enum\Game\GameInitialisationStepEnum;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ClassicGameWithGameMasterSettedAndRandomDispatchStory extends ClassicGameConfiguredWithGameMasterAndRandomDispatchStory
{
    public const GAME_MASTER = 'game_master';

    public function build(): void
    {
        parent::build();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $gameMasterBuilder = $this->getState(self::PLAYER_7);
        \assert($gameMasterBuilder instanceof PlayerBuilder);

        $gameBuilder->withGameMaster($gameMasterBuilder);
        $this->addState(self::GAME_MASTER, $gameMasterBuilder);

        $gameBuilder->withInitialisationStep(GameInitialisationStepEnum::DISPATCH);
    }

    public function getPrefix(): string
    {
        return 'classic-game-with-game-master-setted-and-random-dispatch-';
    }
}
