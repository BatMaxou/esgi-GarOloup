<?php

namespace App\Tests\Functional\Game\Initialisation;

use App\Entity\Event\Game\ReOpenGameInvitationEvent;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class ReOpenGameInvitationTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_anonymous_cant_re_open_game_invitation(): void
    {
        When::game()->reOpenInvitation();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_host_can_re_open_game_invitation(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();
        $gameBuilder = ThereIs::aGame()->withHost($hostBuilder)->withInitialisationStep(GameInitialisationStepEnum::CONFIGURATION)->build();

        $response = When::asUser($userBuilder)->game()->reOpenInvitation();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));
        $this->assertEquals(GameInitialisationStepEnum::NEW, $gameBuilder->getEntity()->getInitialisationStep());
    }

    public function test_user_without_player_cant_re_open_game_invitation(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->game()->reOpenInvitation();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_temp_user_without_player_cant_re_open_game_invitation(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder)->game()->reOpenInvitation();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_player_cant_re_open_game_invitation(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->withInitialisationStep(GameInitialisationStepEnum::CONFIGURATION)->build();
        $playerBuilder = ThereIs::aPlayer()->withUser($userBuilder)->withGame($gameBuilder)->build();

        When::asUser($userBuilder)->game()->reOpenInvitation();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_re_open_game_invitation_if_game_has_already_pass_invitation_step(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();
        $gameBuilder = ThereIs::aGame()->withHost($hostBuilder)->withInitialisationStep(GameInitialisationStepEnum::GAME_MASTER_CHOICE)->build();

        When::asUser($userBuilder)->game()->reOpenInvitation();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_event_dispatched_by_re_open_game_invitation(): void
    {
        $userBuilder = ThereIs::anUser()->withUsername('SLiipMan')->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();
        $gameBuilder = ThereIs::aGame()->withHost($hostBuilder)->withInitialisationStep(GameInitialisationStepEnum::CONFIGURATION)->build();

        $response = When::asUser($userBuilder)->game()->reOpenInvitation();
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(ReOpenGameInvitationEvent::class, $userBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
