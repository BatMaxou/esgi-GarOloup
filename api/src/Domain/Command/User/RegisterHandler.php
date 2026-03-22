<?php

namespace App\Domain\Command\User;

use App\Api\Model\BasicActionOutput;
use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RegisterHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(RegisterCommand $command): BasicActionOutput
    {
        if ($this->userRepository->findOneBy(['email' => $command->email])) {
            throw new ConflictHttpException('You already have an account');
        }

        if ($this->userRepository->findOneBy(['username' => $command->username])) {
            throw new ConflictHttpException('This username is already taken');
        }

        $newUser = new User()
            ->setEmail($command->email)
            ->setUsername($command->username)
            ->setPassword($command->password);

        $this->em->persist($newUser);
        $this->em->flush();

        return new BasicActionOutput(true);
    }
}
