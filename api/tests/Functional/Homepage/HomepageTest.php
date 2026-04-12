<?php

namespace App\Tests\Functional\Homepage;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class HomepageTest extends GarOloupApiTestCase
{
    public function test_anonymous_can_access_homepage(): void
    {
        When::homepage()->get();
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_user_can_access_homepage(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->homepage()->get();
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_temp_user_can_access_homepage(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder)->homepage()->get();
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_it_returns_roles(): void
    {
        ThereIs::aRole()->villager()->build();
        ThereIs::aRole()->werewolf()->build();

        $response = When::homepage()->get();
        $this->assertResponseStatusCodeSame(200);

        $roles = $response->get('[lastRoles]');
        $this->assertIsArray($roles);
        $this->assertCount(2, $roles);
    }

    public function test_it_returns_only_new_public_games(): void
    {
        ThereIs::aGame()->public()->build();
        ThereIs::aGame()->public()->closed()->build();
        ThereIs::aGame()->build();

        $response = When::homepage()->get();
        $this->assertResponseStatusCodeSame(200);

        $games = $response->get('[lastPublicGames]');
        $this->assertIsArray($games);
        $this->assertCount(1, $games);
    }

    public function test_it_does_not_return_private_games(): void
    {
        ThereIs::aGame()->public()->build();
        ThereIs::aGame()->build();

        $response = When::homepage()->get();
        $this->assertResponseStatusCodeSame(200);

        $games = $response->get('[lastPublicGames]');
        $this->assertIsArray($games);
        $this->assertCount(1, $games);
    }

    public function test_it_returns_empty_when_no_data(): void
    {
        $response = When::homepage()->get();
        $this->assertResponseStatusCodeSame(200);

        $roles = $response->get('[lastRoles]');
        $games = $response->get('[lastPublicGames]');
        $this->assertIsArray($roles);
        $this->assertIsArray($games);
        $this->assertCount(0, $roles);
        $this->assertCount(0, $games);
    }

    public function test_it_returns_both_roles_and_public_games(): void
    {
        ThereIs::aRole()->villager()->build();
        ThereIs::aGame()->public()->build();
        ThereIs::aGame()->public()->build();

        $response = When::homepage()->get();
        $this->assertResponseStatusCodeSame(200);

        $roles = $response->get('[lastRoles]');
        $games = $response->get('[lastPublicGames]');
        $this->assertIsArray($roles);
        $this->assertIsArray($games);
        $this->assertCount(1, $roles);
        $this->assertCount(2, $games);
    }
}
