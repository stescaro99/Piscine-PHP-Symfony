<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class E01Controller extends AbstractController
{
    #[Route('/e01', name: 'e01homepage')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('homepages/e01.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/e01/reset', name: 'e01resetdb')]
    public function resetDB(Connection $conn, Request $request): Response
    {
        $conn->executeStatement('DELETE FROM user');
        $conn->executeStatement('DELETE FROM posts');
        $this->addFlash('success', 'Database reset successfully!');
        return $this->redirect($request->headers->get('referer'));
    }
}