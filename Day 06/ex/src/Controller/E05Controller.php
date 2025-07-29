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

class E05Controller extends AbstractController
{
    #[Route('/e05', name: 'e05homepage')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('homepages/e05.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/e05/posts', name: 'e05posts')]
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
                return $this->redirectToRoute('e05posts');
            }
        }
        $posts = $entityManager->getRepository(Post::class)
            ->findBy([], ['created' => 'DESC']);
        return $this->render('posts/posts05.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'form' => $form ? $form->createView() : null,
        ]);
    }

    #[Route('/e05/vote/{id}/{type}', name: 'post_vote')]
    public function votePost(int $id, string $type, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user)
            return $this->redirectToRoute('e05homepage');
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
                return $this->redirectToRoute('e05posts');
            else
            {
                $existingVote->setType($type);
                $entityManager->flush();
                return $this->redirectToRoute('e05posts');
            }
        }
        $vote = new Vote();
        $vote->setUser($user);
        $vote->setPost($post);
        $vote->setType($type);
        $entityManager->persist($vote);
        $entityManager->flush();
        return $this->redirectToRoute('e05posts');
    }

    #[Route('/e05/post/{id}', name: 'post_detail05')]
    public function postDetail(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user)
        {
            return $this->redirectToRoute('e05homepage');
        }
        $post = $entityManager->getRepository(Post::class)->find($id);
        if (!$post)
        {
            throw $this->createNotFoundException('Post not found');
        }
        return $this->render('posts/post_details05.html.twig', [
            'post' => $post,
        ]);
    }
}