<?php

namespace App\Tests\Functional\Game\Initialisation;

use App\Entity\Event\Game\LaunchGameEvent;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class LaunchGameTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_host_can_launch_game(): void
    {
        $hostBuilder = ThereIs::anUser()->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder)->build();
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withInitialisationStep(GameInitialisationStepEnum::FINISH)
            ->build()
        ;

        $response = When::asUser($hostBuilder)->game()->launch();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));
        $this->assertEquals(GameRuntimeStepEnum::SETUP, $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_anonymous_cant_launch_game(): void
    {
        When::game()->launch();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_without_player_cant_launch_game(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->game()->launch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_temp_user_without_player_cant_launch_game(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder)->game()->launch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_random_player_cant_launch_game(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->withInitialisationStep(GameInitialisationStepEnum::FINISH)->build();
        ThereIs::aPlayer()->withUser($userBuilder)->withGame($gameBuilder)->build();

        When::asUser($userBuilder)->game()->launch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_launch_game_if_not_it_is_not_ready(): void
    {
        $hostBuilder = ThereIs::anUser()->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder)->build();
        ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withInitialisationStep(GameInitialisationStepEnum::GAME_MASTER_CHOICE)
            ->build()
        ;

        When::asUser($hostBuilder)->game()->launch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_event_dispatched_by_launch_game(): void
    {
        $hostBuilder = ThereIs::anUser()->withUsername('SLiipMan')->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder)->build();
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withInitialisationStep(GameInitialisationStepEnum::FINISH)
            ->build()
        ;

        When::asUser($hostBuilder)->game()->launch();
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(LaunchGameEvent::class, $hostBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
