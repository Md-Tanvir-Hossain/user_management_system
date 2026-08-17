<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the user's password before saving it.
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );

            $user->setPassword($hashedPassword);

            // New accounts must be verified before becoming active.
            $user->setStatus('unverified');

            // Generate a secure verification token.
            $user->setVerificationToken(
                bin2hex(random_bytes(32))
            );

            // Token expires after 24 hours.
            $user->setVerificationTokenExpiresAt(
                new \DateTimeImmutable('+24 hours')
            );

            $entityManager->persist($user);
            $entityManager->flush();

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