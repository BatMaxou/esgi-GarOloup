<?php

namespace App\Domain\Command\User;

use App\Api\Model\User\ResetPasswordOutput;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ResetPasswordHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(ResetPasswordCommand $command): ResetPasswordOutput
    {
        $user = $this->userRepository->findOneBy(['resetToken' => $command->token]);
        if (!$user) {
            return new ResetPasswordOutput();
        }

        $user->setPassword($command->password);
        $user->setResetToken(null);

        $this->em->flush();

        return new ResetPasswordOutput();
    }
}
