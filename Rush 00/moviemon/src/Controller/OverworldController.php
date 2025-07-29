<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Finder\Finder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

// including the Game Manager service
use App\Service\GameManager;

class OverworldController extends AbstractController
{
    //TODO: aggiungere grandezza della mappa al game manager
    #[Route('/new', name: 'new')]
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function StartNewGame(GameManager $gameManager, Request $request)
    {
        if ($request->isMethod('POST'))
        {
            $username = $request->request->get('player_name');
            $mapSize = (int) $request->request->get('map_size');
            // $map = array($mapS);
            $gameManager->setMapSize($mapSize);
            // $session = $request->getSession();
            // $session->set('map_size', $mapSize);
            // echo $mapSize;
            // print_r($gameManager->getMapSize());
            // exit;

            if (!$username) {
                return new Response("Missing player name.", 400);
            }

            $gameManager->startNewGame($username, $mapSize);
            $user = $gameManager->getUser();
            $user->setPosition([2, 3]);
            $gameManager->setMapSize($mapSize);
            return $this->redirectToRoute('overworld');
        }
        // Fallback nel caso di request GET
        return $this->render('main_menu.html.twig');
    }

        #[Route('/load', name: 'load')]
    public function loadGameMenu(): Response
    {
        $saveDir = __DIR__ . '/../../var/saves';

        $finder = new Finder();
        $finder->files()->in($saveDir)->name('*.json');

        $saves = [];
        foreach ($finder as $file) {
            $filename = $file->getFilenameWithoutExtension();
            $saves[] = $filename;
        }

        return $this->render('load.html.twig', [
            'saves' => $saves,
        ]);
    }

    #[Route('/load/{playerName}', name: 'load_specific_game')]
    public function loadSpecificGame(GameManager $gameManager, string $playerName): RedirectResponse
    {
        $gameManager->loadGame($playerName);

        return $this->redirectToRoute('overworld');
    }

    #[Route('/save', name: 'save')]
    public function saveGame(GameManager $gameManager): Response
    {
        $gameManager->saveGame();
        return $this->redirectToRoute('overworld');
    }

    #[Route('/overworld', name: 'overworld')]
    public function index(GameManager $gameManager): Response
    {
        // print_r($gameManager->getMapSize());
        // exit;
        $map = $this->generateMap($gameManager);
        $toCatch = $this->getRemainingMovies($gameManager);
        $catched = $this->getCatchedMovies($gameManager);
        return $this->render('map.html.twig', [
            'map' => $map,
            'catchable' => $toCatch,
            'catched' => $catched,
        ]);
    }

    public function getRemainingMovies(GameManager $gameManager): array
    {
        $user = $gameManager->getUser();
        // var_dump($user->getRemainingMoviemons());
        $entities = $user->getRemainingMoviemons();
        $remaining = [];

        foreach ($entities as $movie)
        {
            $remaining[] = $movie->getName();
        }
        // print_r($remaining);
        return($remaining);
    }

        public function getCatchedMovies(GameManager $gameManager): array
    {
        $user = $gameManager->getUser();
        // var_dump($user->getRemainingMoviemons());
        $entities = $user->getCapturedMoviemons();
        $remaining = [];

        foreach ($entities as $movie)
        {
            $remaining[] = $movie->getName();
        }
        // print_r($remaining);
        return($remaining);
    }


    public function generateMap(GameManager $gameManager)
    {
        $grid = [];
        // $mapSize = [];
        $mapSize = $gameManager->getMapSize();
        // var_dump($mapSize);

        $rows = $mapSize;
        $columns = $mapSize;

        $user = $gameManager->getUser();
        $playerPosition = $user->getPosition();

        for ($y = 0; $y < $columns; $y++) {
            $row = [];
            for ($x = 0; $x < $rows; $x++) {
                $row[] = " ";
            }
            $grid[] = $row;
        }

        $grid[$playerPosition[0]][$playerPosition[1]] = 'L';

        return ($grid);
    }

    #[Route('/moviedex', name: 'moviedex')]
    public function moviedex(GameManager $gameManager): Response
    {
        $user = $gameManager->getUser();
        $catched = $this->getCatchedMovies($gameManager);
        $moviemons = $user->getCapturedMoviemons();
        return $this->render('moviedex.html.twig', [
            'user' => $user,
            'catched' => $catched,
            'moviemons' => $moviemons,
        ]);
    }
}

?>
