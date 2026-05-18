<?php

namespace App\Domain\Workflow\Interface;

interface NightResettableInterface
{
    public function clearNightState(): void;
}
