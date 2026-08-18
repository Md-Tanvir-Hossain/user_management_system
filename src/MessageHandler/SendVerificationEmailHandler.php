<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\SendVerificationEmail;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler]
final class SendVerificationEmailHandler
{
    public function __construct(
        // private UserRepository $userRepository,
        // private MailerInterface $mailer,
        // private UrlGeneratorInterface $urlGenerator,
        private UserRepository $userRepository,
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator,
        private string $mailerFrom,
    ) {}

    public function __invoke(SendVerificationEmail $message): void
    {
        $user = $this->userRepository->find($message->userId);

        if (!$user instanceof User) {
            return;
        }

        if ($user->getStatus() !== 'unverified') {
            return;
        }

        $token = $user->getVerificationToken();

        if ($token === null) {
            return;
        }

        $verificationUrl = $this->urlGenerator->generate(
            'app_verify_email',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new TemplatedEmail())
            ->from('no-reply@example.com')
            ->to($user->getEmail())
            ->subject('Verify your email address')
            ->htmlTemplate('emails/verification.html.twig')
            ->context([
                'user' => $user,
                'verificationUrl' => $verificationUrl,
            ]);

        $this->mailer->send($email);
    }
}
