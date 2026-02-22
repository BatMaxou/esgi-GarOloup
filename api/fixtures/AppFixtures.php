<?php

namespace App\Fixture;

use App\Tests\Helper\ThereIs;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ThereIs::anUser()->withEmail('test@garoloup.com')->withUsername('Test')->build();
        ThereIs::anUser()->build(10);
    }
}
