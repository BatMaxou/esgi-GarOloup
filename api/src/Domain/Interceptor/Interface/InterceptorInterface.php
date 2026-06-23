<?php

namespace App\Domain\Interceptor\Interface;

use App\Entity\Game\Game;

interface InterceptorInterface
{
    public function hasPendingAction(Game $game): bool;
}
