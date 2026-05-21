<?php

namespace App\Tests\Helper\Builder\Story;

use App\Fixtures\Story\GaroloupStory;
use App\Tests\Helper\Builder\AbstractBuilder;

/**
 * @template T of GaroloupStory
 *
 * @extends AbstractBuilder<T>
 */
class StoryBuilder extends AbstractBuilder
{
    /** @param class-string<T> $storyClass */
    public function __construct(
        public readonly string $storyClass,
    ) {
        $this->build();
    }

    /** @return T */
    public function execute(): GaroloupStory
    {
        if (!$this->entity) {
            throw new \InvalidArgumentException('Try to execute a story that has not been built yet');
        }

        $this->entity->execute();

        return $this->entity;
    }

    protected function doBuild(): object
    {
        return $this->storyClass::load();
    }
}
