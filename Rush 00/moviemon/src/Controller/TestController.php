<?php
namespace App\Controller;

use App\Service\GameManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test/game', name: 'test_game')]
    public function index(GameManager $gameManager): Response
    {
        $user = $gameManager->getUser();

        return $this->render('test/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/test/game/start', name: 'test_game_start')]
    public function startGame(GameManager $gameManager): Response
    {
        $gameManager->startNewGame('Tester');
        return $this->redirectToRoute('test_game');
    }

    #[Route('/test/game/save', name: 'test_game_save')]
    public function saveGame(GameManager $gameManager): Response
    {
        $gameManager->saveGame();
        return $this->redirectToRoute('test_game');
    }

    #[Route('/test/game/load', name: 'test_game_load')]
    public function loadGame(GameManager $gameManager): Response
    {
        $gameManager->loadGame('Tester');
        return $this->redirectToRoute('test_game');
    }

    #[Route('/test/game/reset', name: 'test_game_reset')]
    public function resetGame(GameManager $gameManager): Response
    {
        $gameManager->resetGame();
        return $this->redirectToRoute('test_game');
    }
}
