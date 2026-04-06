<?php

namespace App\Model\Mercure;

use App\Enum\TopicEnum;
use App\Service\Mercure\Inteface\TopicRelatedObject;

class Topic
{
    public bool $private = true;
    public ?string $group;

    public function __construct(
        public readonly TopicEnum $type,
        public readonly TopicRelatedObject $subject,
    ) {
    }

    public function setPrivate(bool $private): static
    {
        $this->private = $private;

        return $this;
    }

    public function setGroup(string $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function __toString(): string
    {
        $topicIdentifier = $this->subject->getTopicIdentifier();
        if (!$topicIdentifier) {
            return $this->type->value;
        }

        return \str_replace('{id}', $topicIdentifier, $this->type->value);
    }
}
