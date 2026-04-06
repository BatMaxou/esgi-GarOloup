<?php

namespace App\Service\Mercure;

use App\Model\Mercure\Topic;

class TopicCollector
{
    /** @var Topic[] */
    private array $topicsToUpdate = [];

    public function collect(Topic $topic): void
    {
        $this->topicsToUpdate[] = $topic;
    }

    /** @return Topic[] */
    public function getTopicsToUpdate(): array
    {
        return $this->topicsToUpdate;
    }
}
