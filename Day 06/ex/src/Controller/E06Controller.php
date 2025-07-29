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
use App\Entity\Vote;

class E06Controller extends AbstractController
{
    #[Route('/e06', name: 'e06homepage')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('homepages/e06.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/e06/posts', name: 'e06posts')]
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
                return $this->redirectToRoute('e06posts');
            }
        }
        $posts = $entityManager->getRepository(Post::class)
            ->findBy([], ['created' => 'DESC']);
        return $this->render('posts/posts06.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'form' => $form ? $form->createView() : null,
        ]);
    }

    #[Route('/e06/vote/{id}/{type}', name: 'post_vote')]
    public function votePost(int $id, string $type, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user)
            return $this->redirectToRoute('e06homepage');
        $post = $entityManager->getRepository(Post::class)->find($id);
        if (!$post)
            throw $this->createNotFoundException('Post not found');
        if (!in_array($type, ['like', 'dislike']))
            throw $this->createNotFoundException('Invalid vote type');
        $voteRepo = $entityManager->getRepository(Vote::class);
        $existingVote = $voteRepo->findOneBy(['user' => $user, 'post' => $post]);
        if ($existingVote)
        {
            if ($existingVote->getType() === $type)
                return $this->redirectToRoute('e06posts');
            else
            {
                $existingVote->setType($type);
                $entityManager->flush();
                return $this->redirectToRoute('e06posts');
            }
        }
        $vote = new Vote();
        $vote->setUser($user);
        $vote->setPost($post);
        $vote->setType($type);
        $entityManager->persist($vote);
        $entityManager->flush();
        return $this->redirectToRoute('e06posts');
    }

    #[Route('/e06/post/{id}', name: 'post_detail06')]
    public function postDetail(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            return $this->redirectToRoute('e06homepage');
        }
        $post = $entityManager->getRepository(Post::class)->find($id);
        if (!$post)
        {
            throw $this->createNotFoundException('Post not found');
        }
        return $this->render('posts/post_details06.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/e06/post/edit/{id}', name: 'post_edit')]
    public function editPost(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user)
            return $this->redirectToRoute('e06homepage');
        $post = $entityManager->getRepository(Post::class)->find($id);
        if (!$post || $post->getAuthor() !== $user)
            throw $this->createNotFoundException('Post not found or you do not have permission to edit it');
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $post->setUpdated(new \DateTimeImmutable());
            $post->setLastEditedBy($user);
            $entityManager->flush();
            return $this->redirectToRoute('post_detail06', ['id' => $post->getId()]);
        }
        return $this->render('posts/post_edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    #[Route('/e06/post/delete/{id}', name: 'post_delete')]
    public function deletePost(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user)
            return $this->redirectToRoute('e06homepage');
        $post = $entityManager->getRepository(Post::class)->find($id);
        if (!$post || $post->getAuthor() !== $user)
            throw $this->createNotFoundException('Post not found or you do not have permission to delete it');
        $entityManager->remove($post);
        $entityManager->flush();
        return $this->redirectToRoute('e06posts');
    }
}