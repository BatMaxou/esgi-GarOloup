<?php

namespace App\Tests\Functional\Game;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class GetPublicGamesTest extends GarOloupApiTestCase
{
    public function test_anonymous_can_access_public_games(): void
    {
        When::game()->getPublicGames();
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_user_can_access_public_games(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->game()->getPublicGames();
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_temp_user_can_access_public_games(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder)->game()->getPublicGames();
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_it_returns_only_public_games(): void
    {
        ThereIs::aGame()->public()->build();
        ThereIs::aGame()->build();

        $response = When::game()->getPublicGames();
        $this->assertResponseStatusCodeSame(200);

        $games = $response->get('[member]');
        $this->assertIsArray($games);
        $this->assertCount(1, $games);
    }

    public function test_it_returns_only_new_public_games(): void
    {
        ThereIs::aGame()->public()->build();
        ThereIs::aGame()->public()->closed()->build();
        ThereIs::aGame()->public()->finished()->build();

        $response = When::game()->getPublicGames();
        $this->assertResponseStatusCodeSame(200);

        $games = $response->get('[member]');
        $this->assertIsArray($games);
        $this->assertCount(1, $games);
    }

    public function test_it_returns_empty_when_no_public_games(): void
    {
        ThereIs::aGame()->build();

        $response = When::game()->getPublicGames();
        $this->assertResponseStatusCodeSame(200);

        $games = $response->get('[member]');
        $this->assertIsArray($games);
        $this->assertCount(0, $games);
    }

    public function test_it_returns_multiple_public_games(): void
    {
        ThereIs::aGame()->public()->build();
        ThereIs::aGame()->public()->build();
        ThereIs::aGame()->public()->build();

        $response = When::game()->getPublicGames();
        $this->assertResponseStatusCodeSame(200);

        $games = $response->get('[member]');
        $this->assertIsArray($games);
        $this->assertCount(3, $games);
    }

    public function test_items_per_page_limits_results(): void
    {
        ThereIs::aGame()->public()->build();
        ThereIs::aGame()->public()->build();
        ThereIs::aGame()->public()->build();

        $response = When::game()->getPublicGames(itemsPerPage: 2);
        $this->assertResponseStatusCodeSame(200);

        $games = $response->get('[member]');
        $this->assertIsArray($games);
        $this->assertCount(2, $games);
    }

    public function test_pagination_returns_correct_page(): void
    {
        ThereIs::aGame()->public()->build();
        ThereIs::aGame()->public()->build();
        ThereIs::aGame()->public()->build();

        $response = When::game()->getPublicGames(page: 2, itemsPerPage: 2);
        $this->assertResponseStatusCodeSame(200);

        $games = $response->get('[member]');
        $this->assertIsArray($games);
        $this->assertCount(1, $games);
    }
}
