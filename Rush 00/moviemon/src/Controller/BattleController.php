<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Moviemon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \App\Service\OmdbApiService;
use App\Service\GameManager;
use Psr\Log\LoggerInterface;

class BattleController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
        {
            $this->logger = $logger;
        }

    #[Route('/battle', name: 'battle')]
    public function index(Request $request, OmdbApiService $omdbApiService, GameManager $gameManager): Response
    {
        $user = $gameManager->getUser();
        if (!$user instanceof User)
            return $this->redirectToRoute('homepage');
        $moviemonName = $request->query->get('moviemon');
        if (!is_string($moviemonName) || $moviemonName === '') {
            $this->addFlash('error', 'No Moviemon specified.');
            return $this->redirectToRoute('overworld');
        }
        $moviemon = $gameManager->getMoviemon($user, $moviemonName);
        if (!$moviemon instanceof Moviemon)
        {
            $this->logger->error('Moviemon not found', ['moviemon' => $moviemonName]);
            $this->addFlash('error', 'Moviemon not found');
            return $this->redirectToRoute('overworld');
        }
        return $this->render('battle.html.twig', [
            'user' => $user,
            'moviemon' => $moviemon
        ]);
    }

    #[Route('/battle/escape', name: 'battle_escape', methods: ['GET'])]
    public function escape(Request $request, GameManager $gameManager): Response
    {
        $user = $gameManager->getUser();
        if (!$user instanceof User)
            return $this->redirectToRoute('homepage');
        $moviemonName = $request->query->get('moviemon');
        if (!is_string($moviemonName) || $moviemonName === '') {
            $this->addFlash('error', 'No Moviemon specified.');
            return $this->redirectToRoute('overworld');
        }
        $moviemon = $gameManager->getMoviemon($user, $moviemonName);
        if (!$moviemon instanceof Moviemon)
        {
            $this->logger->error('Moviemon not found', ['moviemon' => $moviemonName]);
            $this->addFlash('error', 'Moviemon not found');
            return $this->redirectToRoute('overworld');
        }
        if (random_int(0, 4) != 0)
        {
            $this->addFlash('success', 'You escaped successfully!');
            return $this->redirectToRoute('overworld');
        }
        else
        {
            $this->addFlash('error', 'You failed to escape!');
            return $this->redirectToRoute('battle_losehp', ['moviemon' => $moviemon->getName()]);
        }
    }

    #[Route('/battle/fight', name: 'battle_fight', methods: ['GET'])]
    public function fight(Request $request, GameManager $gameManager): Response
    {
        $user = $gameManager->getUser();
        if (!$user instanceof User)
            return $this->redirectToRoute('homepage');
        $moviemonName = $request->query->get('moviemon');
        if (!is_string($moviemonName) || $moviemonName === '') {
            $this->addFlash('error', 'No Moviemon specified.');
            return $this->redirectToRoute('overworld');
        }
        $moviemon = $gameManager->getMoviemon($user, $moviemonName);
        if (!$moviemon instanceof Moviemon)
        {
            $this->logger->error('Moviemon not found', ['moviemon' => $moviemonName]);
            $this->addFlash('error', 'Moviemon not found');
            return $this->redirectToRoute('overworld');
        }
        $damage = random_int(1, $user->getStrength());
        if (random_int(0, 9) < 4)
        {
            $this->addFlash('error', 'You missed your attack!');
            return $this->redirectToRoute('battle_losehp', [
                'moviemon' => $moviemon->getName()
            ]);
        }
        $this->addFlash('success', 'You hit ' . $moviemon->getName() . ' for ' . $damage . ' damage!');
        $moviemon->setHealth(max(0, $moviemon->getHealth() - $damage));
        if ($moviemon->getHealth() <= 0)
        {
            $this->addFlash('success', 'You defeated ' . $moviemon->getName() . '!');
            $user->defeatMoviemon($moviemon);
            if ($user->hasWon()) {
                return $this->redirectToRoute('result', ['victory' => 'true']);
            }
            return $this->redirectToRoute('overworld');
        }
        return $this->redirectToRoute('battle', [
            'moviemon' => $moviemon->getName()
        ]);
    }

    #[Route('/battle/losehp', name: 'battle_losehp', methods: ['GET'])]
    public function loseHp(Request $request, GameManager $gameManager): Response
    {
        $user = $gameManager->getUser();
        if (!$user instanceof User)
            return $this->redirectToRoute('homepage');
        $moviemonName = $request->query->get('moviemon');
        if (!is_string($moviemonName) || $moviemonName === '') {
            $this->addFlash('error', 'No Moviemon specified.');
            return $this->redirectToRoute('overworld');
        }
        $moviemon = $gameManager->getMoviemon($user, $moviemonName);
        if (!$moviemon instanceof Moviemon)
        {
            $this->logger->errorr('Moviemon not found', ['moviemon' => $moviemonName]);
            $this->addFlash('error', 'Moviemon not found');
            return $this->redirectToRoute('overworld');
        }
        $damage = round(random_int(1, $moviemon->getStrength()) / 5);
        $user->setHealth(max(0, $user->getHealth() - $damage));
        if ($user->getHealth() <= 0)
        {
            $this->addFlash('error', 'You were defeated by ' . $moviemon->getName() . '!');
            return $this->redirectToRoute('result', ['victory' => 'false']); // game over page
        }
        return $this->redirectToRoute('battle', [
            'moviemon' => $moviemon->getName()
        ]);
    }

    #[Route('/battle/catch', name: 'battle_catch', methods: ['GET'])]
    public function catch(Request $request, GameManager $gameManager): Response
    {
        $user = $gameManager->getUser();
        if (!$user instanceof User)
            return $this->redirectToRoute('homepage');
        $moviemonName = $request->query->get('moviemon');
        if (!is_string($moviemonName) || $moviemonName === '') {
            $this->addFlash('error', 'No Moviemon specified.');
            return $this->redirectToRoute('overworld');
        }
        $moviemon = $gameManager->getMoviemon($user, $moviemonName);
        if (!$moviemon instanceof Moviemon)
        {
            $this->logger->error('Moviemon not found', ['moviemon' => $moviemonName]);
            $this->addFlash('error', 'Moviemon not found');
            return $this->redirectToRoute('overworld');
        }
        $probability = 100 - round($moviemon->getHealth() / $moviemon->getStrength() * 100);
        if ($probability <= 0)
            $probability = 1;
        if (random_int(0, 100) < $probability)
        {
            $this->addFlash('success', 'You caught ' . $moviemon->getName() . '!');
            $user->defeatMoviemon($moviemon);
            return $this->redirectToRoute('overworld');
        }
        else
        {
            $this->addFlash('error', 'You failed to catch ' . $moviemon->getName() . '!');
            return $this->redirectToRoute('battle_losehp', ['moviemon' => $moviemon->getName()]);
        }
    }

    #[Route('/result/{victory}', name: 'result')]
    public function result(string $victory): Response
    {
        $victoryBool = strtolower($victory) === 'true';

        return $this->render('resultscreen.html.twig', [
            'victory' => $victoryBool,
        ]);
    }
}