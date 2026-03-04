<?php

namespace App\Domain\Command\Email;

use App\Service\Mailer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendForgotPasswordEmailHandler
{
    public function __construct(
        private string $frontResetUrl,
        private readonly Mailer $mailer,
    ) {
    }

    public function __invoke(SendForgotPasswordEmailCommand $command): void
    {
        $this->mailer->sendForgotPasswordEmail($command->user, sprintf('%s/%s', $this->frontResetUrl, $command->token));
    }
}
