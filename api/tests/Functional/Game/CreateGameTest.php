<?php

namespace App\Tests\Functional\Game;

use App\Entity\Event\Game\CreateGameEvent;
use App\Fixtures\Factory\GameFactory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class CreateGameTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_anonymous_cant_create_game(): void
    {
        When::game()->create();
        $this->assertResponseStatusCodeSame(401);

        $this->assertEquals(0, GameFactory::count());
    }

    public function test_user_can_create_game_and_get_join_code(): void
    {
        $userBuilder = ThereIs::anUser()->withEmail('sliipman@garoloup.fr')->withPassword('slaap')->build();

        When::asUser($userBuilder);
        $response = When::game()->create();
        $this->assertResponseStatusCodeSame(201);

        $this->assertEquals(1, GameFactory::count());
        $this->assertNotEmpty($response->get('[joinCode]'));
    }

    public function test_temp_user_can_create_game_and_get_join_code(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder);
        $response = When::game()->create();
        $this->assertResponseStatusCodeSame(201);

        $this->assertEquals(1, GameFactory::count());
        $this->assertNotEmpty($response->get('[joinCode]'));
    }

    public function test_user_cant_create_game_if_already_have_one(): void
    {
        $userBuilder = ThereIs::anUser()->withEmail('sliipman@garoloup.fr')->withPassword('slaap')->build();

        When::asUser($userBuilder);
        When::game()->create();
        $this->assertResponseStatusCodeSame(201);

        When::game()->create();
        $this->assertResponseStatusCodeSame(409);

        $this->assertEquals(1, GameFactory::count());
    }

    public function test_temp_user_cant_create_game_if_already_have_one(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder);
        When::game()->create();
        $this->assertResponseStatusCodeSame(201);

        When::game()->create();
        $this->assertResponseStatusCodeSame(409);

        $this->assertEquals(1, GameFactory::count());
    }

    public function test_game_event_dispatched_by_game_creation(): void
    {
        $userBuilder = ThereIs::anUser()->withUsername('SLiipMan')->build();

        When::asUser($userBuilder);
        $response = When::game()->create();
        $this->assertResponseStatusCodeSame(201);

        $joinCode = $response->get('[joinCode]');
        $game = GameFactory::findBy(['joinCode' => $joinCode])[0] ?? null;
        $this->assertNotNull($game);

        $this->assertCollected(CreateGameEvent::class, $userBuilder->username, $game->getId());
        $this->assertEventCollectedNumber(1);
    }
}
