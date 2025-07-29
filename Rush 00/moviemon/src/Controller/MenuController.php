<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

// including the Game Manager service
use App\Service\GameManager;

class MenuController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(Request $request): Response
    {
        // Controlla se la richiesta proviene dall'Overworld
        $fromOverworld = $request->query->get('from') === 'overworld';

        return $this->render('menu.html.twig', [
            'fromOverworld' => $fromOverworld,
        ]);
    }
}
