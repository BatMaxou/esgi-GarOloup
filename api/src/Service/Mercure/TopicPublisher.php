<?php

namespace App\Service\Mercure;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

class TopicPublisher
{
    public function __construct(
        private readonly HubInterface $hub,
        private readonly TopicCollector $topicCollector,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function publish(): void
    {
        foreach ($this->topicCollector->getTopicsToUpdate() as $topic) {
            $serialized = $this->serializer->serialize($topic->subject, 'json', ['groups' => $topic->group ? [$topic->group] : []]);
            $update = new Update($topic, $serialized, $topic->private);

            $this->hub->publish($update);
        }
    }
}
