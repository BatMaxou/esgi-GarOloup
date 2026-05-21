<?php

namespace App\Fixtures\Story;

use Zenstruck\Foundry\Story;

abstract class GaroloupStory extends Story
{
    abstract public function execute(): void;

    protected function getPrefix(): string
    {
        return '';
    }
}
