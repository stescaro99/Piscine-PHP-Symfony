<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PostController extends AbstractController
{
    #[Route('/', name: 'app_home_nolocale', methods: ['GET'])]
    public function homeRedirect(Request $request): Response
    {
        $queryLocale = $request->query->get('_locale');
        $session = $request->getSession();
        $sessionLocale = $session->get('_locale');
        $locale = $queryLocale ?: ($sessionLocale ?: $request->getLocale());
        $session->set('_locale', $locale);
        if ($queryLocale && $queryLocale !== $request->getLocale()) {
            return $this->redirectToRoute('app_home', ['_locale' => $queryLocale]);
        }
        return $this->redirectToRoute('app_home', ['_locale' => $locale]);
    }
    #[Route('/post-list', name: 'post_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function postList(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['id' => 'DESC']);
        $data = array_map(function($post)
    {
            return [
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
                'created' => $post->getCreated() ? $post->getCreated()->format('d/m/Y H:i') : ''
            ];
        }, $posts);
        return $this->json(['posts' => $data]);
    }
    #[Route('/post', name: 'post_page', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function postPage(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['id' => 'DESC']);
        return $this->render('post.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/post', name: 'post_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createPost(Request $request, PostRepository $postRepository, \Doctrine\ORM\EntityManagerInterface $em, \Symfony\Contracts\Translation\TranslatorInterface $translator): Response
    {
        if (!$request->isXmlHttpRequest())
        {
            return $this->json(['success' => false, 'error' => 'Invalid request'], 400);
        }
        $title = $request->request->get('title');
        $content = $request->request->get('content');
        if (empty($title) || empty($content))
        {
            return $this->json(['success' => false, 'error' => 'Title and content are required'], 400);
        }
        $existing = $postRepository->findOneBy(['title' => $title]);
        if ($existing) {
            return $this->json(['success' => false, 'error' => $translator->trans('post.unique_error')], 409);
        }
        $post = new Post();
        $post->setTitle($title);
        $post->setContent($content);
        $post->setCreatedValue();
        $em->persist($post);
        $em->flush();
        return $this->json(['success' => true]);
    }

    #[Route('/{_locale}', name: 'app_home', requirements: ['_locale' => 'en|it'], methods: ['GET'])]
    public function index(PostRepository $postRepository, Request $request): Response
    {
        $session = $request->getSession();
        $queryLocale = $request->query->get('_locale');
        $sessionLocale = $session->get('_locale');
        $urlLocale = $request->getLocale();
        $locale = $queryLocale ?: ($sessionLocale ?: $urlLocale);
        $session->set('_locale', $locale);
        if ($locale !== $urlLocale) {
            return $this->redirectToRoute('app_home', ['_locale' => $locale]);
        }
        $posts = $postRepository->findBy([], ['id' => 'DESC']);
        return $this->render('index.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/view/{id}', name: 'post_detail', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function postDetail($id, PostRepository $postRepository, Request $request): Response
    {
        $post = $postRepository->find($id);
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }
        if ($request->isXmlHttpRequest()) {
            return $this->render('post_detail.html.twig', [
                'post' => $post
            ]);
        }
        return $this->render('post_detail.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/delete/{id}', name: 'post_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deletePost($id, PostRepository $postRepository, \Doctrine\ORM\EntityManagerInterface $em, Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json(['success' => false, 'error' => 'Invalid request'], 400);
        }
        $post = $postRepository->find($id);
        if (!$post) {
            return $this->json(['success' => false, 'error' => 'Post not found'], 404);
        }
        $em->remove($post);
        $em->flush();
        return $this->json(['success' => true, 'id' => $post->getId()]);
    }
}