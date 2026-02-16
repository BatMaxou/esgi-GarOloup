<?php

namespace App\DataFixtures;

use App\DataFixtures\Faker\FakerFixtureTrait;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    use FakerFixtureTrait;

    protected ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->createUser('test@garoloup.com', 'Test', 'User');
        $this->createUsers(4);

        $this->manager->flush();
    }

    protected function createUsers(int $count): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $user = $this->createUser();
        }
    }

    private function createUser(?string $email = null, ?string $username = null): User
    {
        $user = new User()
            ->setEmail($email ?? $this->faker->email())
            ->setUsername($username ?? $this->faker->userName())
            ->setPassword('azertyuiAZ123#');

        $this->manager->persist($user);

        return $user;
    }
}
