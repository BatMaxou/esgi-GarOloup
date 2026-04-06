<?php

namespace App\Service\Mercure;

use App\Enum\TopicEnum;
use App\Service\Mercure\Exception\UnknownTopicException;

class TopicPrivacyMapper
{
    public static function getPrivacyFor(TopicEnum $type): bool
    {
        return match (true) {
            TopicEnum::CURRENT_GAME === $type => true,
            TopicEnum::CURRENT_PLAYER === $type => true,
            // $type === TopicEnum::CURRENT_GAME_PLAYER => 'TODO',
            default => throw new UnknownTopicException(\sprintf('Unknown topic type "%s"', $type->value)),
        };
    }
}
