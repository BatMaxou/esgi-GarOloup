<?php

namespace App\Tests\Functional\Game;

use App\Entity\Event\Game\CloseGameInvitationEvent;
use App\Enum\Game\GameStepEnum;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class CloseGameInvitationTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_anonymous_cant_close_game_invitation(): void
    {
        When::game()->closeInvitation();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_host_can_close_game_invitation(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();
        $gameBuilder = ThereIs::aGame()->withHost($hostBuilder)->build();

        $response = When::asUser($userBuilder)->game()->closeInvitation();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));
        $this->assertEquals(GameStepEnum::CONFIGURATION, $gameBuilder->getEntity()->getStep());
    }

    public function test_user_without_player_cant_close_game_invitation(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->game()->closeInvitation();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_temp_user_without_player_cant_close_game_invitation(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder)->game()->closeInvitation();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_player_cant_close_game_invitation(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->build();
        $playerBuilder = ThereIs::aPlayer()->withUser($userBuilder)->withGame($gameBuilder)->build();

        When::asUser($userBuilder)->game()->closeInvitation();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_close_game_invitation_if_game_has_already_pass_invitation_step(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();
        $gameBuilder = ThereIs::aGame()->withHost($hostBuilder)->withStep(GameStepEnum::CONFIGURATION)->build();

        When::asUser($userBuilder)->game()->closeInvitation();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_event_dispatched_by_close_game_invitation(): void
    {
        $userBuilder = ThereIs::anUser()->withUsername('SLiipMan')->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();
        $gameBuilder = ThereIs::aGame()->withHost($hostBuilder)->build();

        $response = When::asUser($userBuilder)->game()->closeInvitation();
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(CloseGameInvitationEvent::class, $userBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
