<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VerificationController extends AbstractController
{
    #[Route('/verify/{token}', name: 'app_verify_email', methods: ['GET'])]
    public function verify(
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $userRepository->findOneBy([
            'verificationToken' => $token,
        ]);

        if (!$user instanceof User) {
            throw $this->createNotFoundException('Invalid verification link.');
        }

        if ($user->getStatus() !== 'unverified') {
            $this->addFlash('info', 'This account has already been verified.');

            return $this->redirectToRoute('app_login');
        }

        $expiresAt = $user->getVerificationTokenExpiresAt();

        if ($expiresAt === null || $expiresAt < new \DateTimeImmutable()) {
            throw $this->createAccessDeniedException(
                'This verification link has expired.'
            );
        }

        // Verify the account.
        $user->setStatus('active');

        // Token can no longer be reused.
        $user->setVerificationToken(null);
        $user->setVerificationTokenExpiresAt(null);

        $user->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Your email has been verified successfully. You can now log in.'
        );

        return $this->redirectToRoute('app_login');
    }
}