<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UserStateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private EntityManagerInterface $entityManager,
        private RouterInterface $router,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (
            $request->attributes->get('_route') === 'app_login' ||
            $request->attributes->get('_route') === 'app_logout'
        ) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            return;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return;
        }

        // Reload the user from the database.
        $freshUser = $this->entityManager
            ->getRepository(User::class)
            ->find($user->getId());

        // User was deleted.
        if (!$freshUser instanceof User) {
            $this->forceLogout($event);
            return;
        }

        // User was blocked.
        if ($freshUser->getStatus() === 'blocked') {
            $this->forceLogout($event);
            return;
        }

        // User became unverified.
        if ($freshUser->getStatus() === 'unverified') {
            $this->forceLogout($event);
            return;
        }
    }

    private function forceLogout(RequestEvent $event): void
    {
        $this->tokenStorage->setToken(null);

        $event->setResponse(
            new RedirectResponse(
                $this->router->generate('app_login')
            )
        );
    }
}