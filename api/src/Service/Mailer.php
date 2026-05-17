<?php

namespace App\Service;

use App\Entity\User\User;
use App\Enum\EmailTypeEnum;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Exception\InvalidArgumentException;

class Mailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    public function sendForgotPasswordEmail(User $user, string $resetUrl): void
    {
        $this->send(
            $user,
            'Réinitialisation de mot de passe',
            EmailTypeEnum::FORGOT_PASSWORD,
            [
                'username' => $user->getUsername(),
                'resetUrl' => $resetUrl,
            ]
        );
    }

    /**
     * @param array<string, mixed> $context
     */
    private function send(User $user, string $subject, EmailTypeEnum $type, array $context = []): void
    {
        $to = $user->getEmail();
        if (!$to) {
            throw new InvalidArgumentException('User email is required');
        }

        $template = new TemplatedEmail()
            ->to($to)
            ->from('garoloup.game@gmail.com')
            ->subject($subject)
            ->htmlTemplate(\sprintf('email/%s.html.twig', $type->value))
            ->context($context);

        $this->mailer->send($template);
    }
}
