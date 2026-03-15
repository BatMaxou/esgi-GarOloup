<?php

namespace App\Domain\GameEvent\Applicator\Trait;

use App\Domain\GameEvent\Applicator\Exception\MissingUserException;
use App\Entity\Event\Game\GameEvent;
use App\Entity\User\AbstractUser;

trait UserAwareTrait
{
    private function ensureUser(GameEvent $event): AbstractUser
    {
        $user = $event->getUser();
        if (!$user) {
            throw new MissingUserException('User must be set at this step');
        }

        return $user;
    }
}
