<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\PostType;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class E03Controller extends AbstractController
{
    #[Route('/e03', name: 'e03homepage')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('homepages/e03.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/e03/posts', name: 'e03posts')]
    public function posts(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $post = new Post();
        $form = null;
        if ($user)
        {
            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid())
            {
                $post->setAuthor($user);
                $post->setCreated(new \DateTimeImmutable());
                $entityManager->persist($post);
                $entityManager->flush();
                return $this->redirectToRoute('e03posts');
            }
        }
        $posts = $entityManager->getRepository(Post::class)
            ->findBy([], ['created' => 'DESC']);
        return $this->render('posts/posts.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'form' => $form ? $form->createView() : null,
        ]);
    }

    #[Route('/e03/post/{id}', name: 'post_detail')]
    public function postDetail(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            return $this->redirectToRoute('e03homepage');
        }
        $post = $entityManager->getRepository(Post::class)->find($id);
        if (!$post)
        {
            throw $this->createNotFoundException('Post not found');
        }
        return $this->render('posts/post_details.html.twig', [
            'post' => $post,
        ]);
    }
}