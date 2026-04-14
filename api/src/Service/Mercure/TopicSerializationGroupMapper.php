<?php

namespace App\Service\Mercure;

use App\Enum\TopicEnum;
use App\Service\Mercure\Exception\UnknownTopicException;

class TopicSerializationGroupMapper
{
    public static function getSerializeGroupFor(TopicEnum $type): string
    {
        return match (true) {
            TopicEnum::CURRENT_GAME === $type => 'game:read',
            TopicEnum::CURRENT_PLAYER === $type => 'me:player:read',
            TopicEnum::WEREWOLF_TEAM === $type => 'werewolf-team:read',
            default => throw new UnknownTopicException(\sprintf('Unknown topic type "%s"', $type->value)),
        };
    }
}
