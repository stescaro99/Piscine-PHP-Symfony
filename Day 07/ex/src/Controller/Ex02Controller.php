<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Ex02Controller extends AbstractController
{
    #[Route('/ex02', name: 'ex02_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('ex02_locale', [
            '_locale' => 'en',
            'count' => 0
        ]);
    }

    #[Route('/{_locale}/ex02/{count}', name: 'ex02_locale', requirements: [
        '_locale' => 'en|fr',
        'count' => '[0-9]'
    ], defaults: ['count' => 0])]
    public function translationsAction(string $_locale, int $count): Response
    {
        $number = $this->getParameter('d07.number');
        return $this->render('ex02/index.html.twig', [
            'number' => $number,
            'count' => $count,
            '_locale' => $_locale
        ]);
    }
}