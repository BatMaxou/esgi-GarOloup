<?php

namespace App\Entity\Game\Period\Interface;

interface TargetableActionInterface
{
    public function getTargetPlayerId(): string;
}
