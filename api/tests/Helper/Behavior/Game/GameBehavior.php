<?php

namespace App\Tests\Helper\Behavior\Game;

use App\Tests\Helper\Behavior\AbstractBehavior;
use App\Tests\Helper\Behavior\BehaviorResponse;
use App\Tests\Helper\Builder\Game\CompositionBuilder;
use App\Tests\Helper\Builder\Game\DispatchBuilder;

class GameBehavior extends AbstractBehavior
{
    public function create(int $maxPlayers = 10, int $maxTimeForDiscussion = 5, bool $public = false): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('POST', '/api/games', [
            'json' => [
                'maxPlayers' => $maxPlayers,
                'maxTimeForDiscussion' => $maxTimeForDiscussion,
                'public' => $public,
            ],
        ]));
    }

    public function join(?string $joinCode): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('POST', '/api/game/join', [
            'json' => [
                'joinCode' => $joinCode,
            ],
        ]));
    }

    public function getCurrent(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('GET', '/api/game'));
    }

    public function getPublicGames(int $page = 1, ?int $itemsPerPage = null): BehaviorResponse
    {
        $query = ['page' => $page];
        if (null !== $itemsPerPage) {
            $query['itemsPerPage'] = $itemsPerPage;
        }

        return new BehaviorResponse($this->client->request('GET', '/api/games/public', [
            'query' => $query,
        ]));
    }

    public function closeInvitation(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('PATCH', '/api/game/invitation/close', [
            'headers' => $this->getPatchHeaders(),
            'json' => [],
        ]));
    }

    public function reOpenInvitation(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('PATCH', '/api/game/invitation/open', [
            'headers' => $this->getPatchHeaders(),
            'json' => [],
        ]));
    }

    public function setGameMaster(?string $playerId): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('PATCH', '/api/game/game-master', [
            'headers' => $this->getPatchHeaders(),
            'json' => [
                'playerId' => $playerId,
            ],
        ]));
    }

    public function setConfiguration(?CompositionBuilder $composition, bool $withGameMaster = false, bool $withRandomDispatch = true): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('PATCH', '/api/game/configuration', [
            'headers' => $this->getPatchHeaders(),
            'json' => [
                'composition' => $composition?->toInput(),
                'withGameMaster' => $withGameMaster,
                'withRandomDispatch' => $withRandomDispatch,
            ],
        ]));
    }

    public function dispatchRoles(DispatchBuilder $dispatch): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('PATCH', '/api/game/role-dispatch', [
            'headers' => $this->getPatchHeaders(),
            'json' => [
                'dispatch' => $dispatch->toInput(),
            ],
        ]));
    }

    public function resetRoleDispatch(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('PATCH', '/api/game/role-dispatch/reset', [
            'headers' => $this->getPatchHeaders(),
            'json' => [],
        ]));
    }

    public function launch(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('PATCH', '/api/game/launch', [
            'headers' => $this->getPatchHeaders(),
            'json' => [],
        ]));
    }

    public function getWerewolfTeam(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('GET', '/api/game/werewolf-team'));
    }

    public function werewolfVote(?string $targetPlayerId): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('PATCH', '/api/game/night/werewolf', [
            'headers' => $this->getPatchHeaders(),
            'json' => [
                'targetPlayerId' => $targetPlayerId,
            ],
        ]));
    }

    public function vote(?string $targetPlayerId): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('PATCH', '/api/game/vote', [
            'headers' => $this->getPatchHeaders(),
            'json' => [
                'targetPlayerId' => $targetPlayerId,
            ],
        ]));
    }

    public function timeUp(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('PATCH', '/api/game/time-up', [
            'headers' => $this->getPatchHeaders(),
            'json' => [],
        ]));
    }
}
