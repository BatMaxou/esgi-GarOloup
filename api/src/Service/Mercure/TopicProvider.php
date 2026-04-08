<?php

namespace App\Service\Mercure;

use App\Enum\TopicEnum;
use App\Model\Mercure\Topic;
use App\Service\Mercure\Inteface\TopicRelatedObject;

class TopicProvider
{
    public function provide(TopicEnum $topicEnum, TopicRelatedObject $topicRelatedObject): Topic
    {
        $topic = new Topic($topicEnum, $topicRelatedObject)
            ->setGroup(TopicSerializationGroupMapper::getSerializeGroupFor($topicEnum))
            ->setPrivate(TopicPrivacyMapper::getPrivacyFor($topicEnum));

        return $topic;
    }
}
