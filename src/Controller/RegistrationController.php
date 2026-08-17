<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Message\SendVerificationEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        MessageBusInterface $messageBus
    ): Response {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );

            $user->setPassword($hashedPassword);

            // New users must verify their email.
            $user->setStatus('unverified');

            // Generate a secure 64-character verification token.
            $user->setVerificationToken(
                bin2hex(random_bytes(32))
            );

            // Token expires after 24 hours.
            $user->setVerificationTokenExpiresAt(
                new \DateTimeImmutable('+24 hours')
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // Send verification email asynchronously.
            $messageBus->dispatch(
                new SendVerificationEmail($user->getId())
            );

            $this->addFlash(
                'success',
                'Registration successful! Please check your email to verify your account.'
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}