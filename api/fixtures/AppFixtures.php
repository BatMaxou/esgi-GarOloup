<?php

namespace App\Fixtures;

use App\Fixtures\Story\ClassicGame\ClassicGameNight1WerewolfTurnStory;
use App\Tests\Helper\ThereIs;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ThereIs::anAdmin()->withEmail('admin@garoloup.com')->withUsername('Admin')->build();
        ThereIs::anUser()->withEmail('test@garoloup.com')->withUsername('Test')->build();
        ThereIs::anUser()->build(10);

        ThereIs::aStory(ClassicGameNight1WerewolfTurnStory::class);
    }
}
