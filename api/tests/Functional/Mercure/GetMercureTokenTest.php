<?php

namespace App\Tests\Functional\Mercure;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class GetMercureTokenTest extends GarOloupApiTestCase
{
    /** @return array<mixed> */
    private function getTopics(string $jwt): array
    {
        $payload = \json_decode(\base64_decode(\explode('.', $jwt)[1]), true);
        $this->assertIsArray($payload);
        $this->assertArrayHasKey('mercure', $payload);
        $this->assertIsArray($payload['mercure']);
        $this->assertArrayHasKey('subscribe', $payload['mercure']);
        $this->assertIsArray($payload['mercure']['subscribe']);

        return $payload['mercure']['subscribe'];
    }

    public function test_anonymous_can_not_get_token(): void
    {
        $response = When::mercure()->getToken();

        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_without_player_has_empty_token(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        $response = When::asUser($userBuilder)->mercure()->getToken();
        $this->assertResponseStatusCodeSame(200);

        $token = $response->get('[token]');
        $this->assertIsString($token);

        $topics = $this->getTopics($token);
        $this->assertCount(0, $topics);
    }

    public function test_user_with_player_gets_basic_player_topic(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->build();
        $playerBuilder = ThereIs::aPlayer()
            ->withUser($userBuilder)
            ->withGame($gameBuilder)
            ->build();

        $response = When::asUser($userBuilder)->mercure()->getToken();
        $this->assertResponseStatusCodeSame(200);

        $token = $response->get('[token]');
        $this->assertIsString($token);

        $topics = $this->getTopics($token);
        $this->assertContains(\sprintf('garoloup-player-%s', (string) $playerBuilder->getEntity()->getId()), $topics);
        $this->assertContains(\sprintf('garoloup-game-%s', (string) $gameBuilder->getEntity()->getId()), $topics);
    }

    public function test_villager_gets_only_villager_topics(): void
    {
        $roleBagBuilder = ThereIs::aRoleBag()->build();
        $gameRoleBagBuilder = ThereIs::aGameRoleBag($roleBagBuilder)->build();

        $userBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->build();
        $playerBuilder = ThereIs::aPlayer()
            ->withUser($userBuilder)
            ->withGame($gameBuilder)
            ->withRole($gameRoleBagBuilder->getVillager())
            ->build();

        $response = When::asUser($userBuilder)->mercure()->getToken();
        $this->assertResponseStatusCodeSame(200);

        $token = $response->get('[token]');
        $this->assertIsString($token);

        $topics = $this->getTopics($token);
        $this->assertCount(2, $topics);
        $this->assertContains(\sprintf('garoloup-player-%s', (string) $playerBuilder->getEntity()->getId()), $topics);
        $this->assertContains(\sprintf('garoloup-game-%s', (string) $gameBuilder->getEntity()->getId()), $topics);
    }

    public function test_werewolf_gets_only_werewolf_topics(): void
    {
        $roleBagBuilder = ThereIs::aRoleBag()->build();
        $gameRoleBagBuilder = ThereIs::aGameRoleBag($roleBagBuilder)->build();

        $userBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->build();
        $playerBuilder = ThereIs::aPlayer()
            ->withUser($userBuilder)
            ->withGame($gameBuilder)
            ->withRole($gameRoleBagBuilder->getWerewolf())
            ->build()
        ;

        $response = When::asUser($userBuilder)->mercure()->getToken();
        $this->assertResponseStatusCodeSame(200);

        $token = $response->get('[token]');
        $this->assertIsString($token);

        $topics = $this->getTopics($token);
        $this->assertCount(3, $topics);
        $this->assertContains(\sprintf('garoloup-player-%s', (string) $playerBuilder->getEntity()->getId()), $topics);
        $this->assertContains(\sprintf('garoloup-game-%s', (string) $gameBuilder->getEntity()->getId()), $topics);
        $this->assertContains(\sprintf('garoloup-werewolf-team-%s', (string) $gameBuilder->getEntity()->getId()), $topics);
    }
}
