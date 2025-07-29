<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class E02Controller extends AbstractController
{
    #[Route('/e02', name: 'e02homepage')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('homepages/e02.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/e02/users', name: 'e02admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $users = $userRepository->findAll();
        $currentUser = $this->getUser();
        return $this->render('admin/users.html.twig', [
            'users' => $users,
            'currentUser' => $currentUser,
        ]);
    }

    #[Route('/e02/delete/{id}', name: 'admin_delete_user', methods: ['POST'])]
    public function deleteUser(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $em->getRepository(User::class)->find($id);
        if (!$user)
            return $this->redirectToRoute('e02admin_users');
        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete-user-' . $user->getId(), $submittedToken)) {
            if ($user->getId() !== $this->getUser()->getId()) {
                $em->remove($user);
                $em->flush();
            }
        }
        return $this->redirectToRoute('e02admin_users');
    }
}