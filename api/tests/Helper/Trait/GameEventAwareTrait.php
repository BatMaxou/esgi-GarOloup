<?php

namespace App\Tests\Helper\Trait;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Mock\GameEvent\MockGameEventCollector;

trait GameEventAwareTrait
{
    protected MockGameEventCollector $gameEventCollector;

    protected function initGameEventCollector(): void
    {
        \assert($this instanceof GarOloupApiTestCase, 'This trait can only be used in a GarOloupApiTestCase.');

        $this->gameEventCollector = $this->getService(MockGameEventCollector::class);
    }

    protected function assertCollected(string $gameEventClass, ?string $playerUsername, ?string $gameId): void
    {
        $this->assertNotNull($playerUsername);
        $this->assertNotNull($gameId);

        if (!isset($this->gameEventCollector)) {
            $this->initGameEventCollector();
        }

        $this->assertTrue($this->gameEventCollector->hasCollected($gameEventClass, $playerUsername, $gameId));
    }

    protected function assertEventCollectedNumber(int $number): void
    {
        if (!isset($this->gameEventCollector)) {
            $this->initGameEventCollector();
        }

        $this->assertEquals($number, $this->gameEventCollector->getEventCollectedNumber());
    }
}
