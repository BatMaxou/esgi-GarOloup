<?php

namespace App\DataFixtures\Faker;

use Faker\Factory;
use Faker\Generator;

trait FakerFixtureTrait
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }
}
