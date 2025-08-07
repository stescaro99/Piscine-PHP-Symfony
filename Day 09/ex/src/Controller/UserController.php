<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)->findBy([], ['id' => 'DESC']);
        return $this->render('security/login.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/signup', name: 'signup_form', methods: ['GET'])]
    public function signupForm(): Response
    {
        return $this->render('security/signup.html.twig');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
    }
    
    #[Route('/signup', name: 'app_signup', methods: ['POST'])]
    public function signup(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $email = $request->request->get('email');
        $plainPassword = $request->request->get('password');
        if (!$email || !$plainPassword) {
            return new JsonResponse(['success' => false, 'error' => 'Email e password obbligatorie'], 400);
        }
        $existing = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existing) {
            return new JsonResponse(['success' => false, 'error' => 'Email già registrata'], 409);
        }
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
        $em->persist($user);
        $em->flush();
        return new JsonResponse(['success' => true]);
    }
}