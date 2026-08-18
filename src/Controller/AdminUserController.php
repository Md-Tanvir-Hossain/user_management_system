<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminUserController extends AbstractController
{
    #[Route('/admin/users', name: 'app_admin_users', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager
            ->getRepository(User::class)
            ->createQueryBuilder('u')
            ->orderBy('u.lastLoginAt', 'DESC')
            ->addOrderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('admin_user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/block', name: 'app_admin_users_block', methods: ['POST'])]
    public function block(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid(
            'admin_users_action',
            $request->request->get('_token')
        )) {
            $this->addFlash('error', 'Invalid security token.');

            return $this->redirectToRoute('app_admin_users');
        }

        $ids = $request->request->all('user_ids');

        if (empty($ids)) {
            $this->addFlash('error', 'No users were selected.');

            return $this->redirectToRoute('app_admin_users');
        }

        $blockedCount = 0;

        foreach ($ids as $id) {
            if (!ctype_digit((string) $id)) {
                continue;
            }

            $user = $entityManager
                ->getRepository(User::class)
                ->find((int) $id);

            if (!$user instanceof User) {
                continue;
            }

            if ($user->getStatus() !== 'blocked') {
                $user->setStatus('blocked');
                $user->setUpdatedAt(new \DateTimeImmutable());

                $blockedCount++;
            }
        }

        $entityManager->flush();

        if ($blockedCount > 0) {
            $this->addFlash(
                'success',
                sprintf('%d user(s) blocked successfully.', $blockedCount)
            );
        } else {
            $this->addFlash(
                'error',
                'No users needed to be blocked.'
            );
        }

        return $this->redirectToRoute('app_admin_users');
    }


    #[Route('/admin/users/unblock', name: 'app_admin_users_unblock', methods: ['POST'])]
    public function unblock(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid(
            'admin_users_action',
            $request->request->get('_token')
        )) {
            $this->addFlash('error', 'Invalid security token.');

            return $this->redirectToRoute('app_admin_users');
        }

        $ids = $request->request->all('user_ids');

        if (empty($ids)) {
            $this->addFlash('error', 'No users were selected.');

            return $this->redirectToRoute('app_admin_users');
        }

        $unblockedCount = 0;

        foreach ($ids as $id) {
            if (!ctype_digit((string) $id)) {
                continue;
            }

            $user = $entityManager
                ->getRepository(User::class)
                ->find((int) $id);

            if (!$user instanceof User) {
                continue;
            }

            if ($user->getStatus() === 'blocked') {
                $user->setStatus('active');
                $user->setUpdatedAt(new \DateTimeImmutable());

                $unblockedCount++;
            }
        }

        $entityManager->flush();

        if ($unblockedCount > 0) {
            $this->addFlash(
                'success',
                sprintf('%d user(s) unblocked successfully.', $unblockedCount)
            );
        } else {
            $this->addFlash(
                'error',
                'No blocked users were selected.'
            );
        }

        return $this->redirectToRoute('app_admin_users');
    }


    #[Route('/admin/users/delete', name: 'app_admin_users_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid(
            'admin_users_action',
            $request->request->get('_token')
        )) {
            $this->addFlash('error', 'Invalid security token.');

            return $this->redirectToRoute('app_admin_users');
        }

        $ids = $request->request->all('user_ids');

        if (empty($ids)) {
            $this->addFlash('error', 'No users were selected.');

            return $this->redirectToRoute('app_admin_users');
        }

        $deletedCount = 0;

        foreach ($ids as $id) {
            if (!ctype_digit((string) $id)) {
                continue;
            }

            $user = $entityManager
                ->getRepository(User::class)
                ->find((int) $id);

            if (!$user instanceof User) {
                continue;
            }

            $entityManager->remove($user);
            $deletedCount++;
        }

        $entityManager->flush();

        if ($deletedCount > 0) {
            $this->addFlash(
                'success',
                sprintf('%d user(s) deleted successfully.', $deletedCount)
            );
        } else {
            $this->addFlash(
                'error',
                'No valid users were found.'
            );
        }

        return $this->redirectToRoute('app_admin_users');
    }


    #[Route(
        '/admin/users/delete-unverified',
        name: 'app_admin_users_delete_unverified',
        methods: ['POST']
    )]
    public function deleteUnverified(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid(
            'admin_users_action',
            $request->request->get('_token')
        )) {
            $this->addFlash('error', 'Invalid security token.');

            return $this->redirectToRoute('app_admin_users');
        }

        $ids = $request->request->all('user_ids');

        if (empty($ids)) {
            $this->addFlash('error', 'No users were selected.');

            return $this->redirectToRoute('app_admin_users');
        }

        $deletedCount = 0;

        foreach ($ids as $id) {
            if (!ctype_digit((string) $id)) {
                continue;
            }

            $user = $entityManager
                ->getRepository(User::class)
                ->find((int) $id);

            if (!$user instanceof User) {
                continue;
            }

            // Only delete unverified users.
            if ($user->getStatus() !== 'unverified') {
                continue;
            }

            $entityManager->remove($user);
            $deletedCount++;
        }

        $entityManager->flush();

        if ($deletedCount > 0) {
            $this->addFlash(
                'success',
                sprintf(
                    '%d unverified user(s) deleted successfully.',
                    $deletedCount
                )
            );
        } else {
            $this->addFlash(
                'error',
                'No selected users were unverified.'
            );
        }

        return $this->redirectToRoute('app_admin_users');
    }
}
