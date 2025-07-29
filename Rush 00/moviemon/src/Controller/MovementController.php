<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// including the Game Manager service
use App\Service\GameManager;

class MovementController extends AbstractController
{

    #[Route('/move/{dir}', name: 'move-player', methods: ['GET'])]

    public function movePlayer(string $dir, GameManager $gameManager): Response
    {

        $user = $gameManager->getUser();
        $position = $user->getPosition();

        $x = $position[0];
        $y = $position[1];

        $size = ($gameManager->getMapSize() - 1);

        // exit;

        // Handle direction
        // max prevent to go above 0
        // index for assumes map size 5
        // add the map size to the gameManager to solve
        switch ($dir) {
            case 'up':
                $x = max(0, $x - 1);
                break;
            case 'down':
                $x = min($size, $x + 1);
                break;
            case 'left':
                $y = max(0, $y - 1);
                break;
            case 'right':
                $y = min($size, $y + 1);
                break;
        }

        $user->setPosition([$x, $y]);

        $moviemon = $gameManager->rand_encounter($user->getRemainingMoviemons());
        if ($moviemon !== null)
        {
            return $this->redirectToRoute('battle', [
                'moviemon' => $moviemon->getName()
            ]);
        }
        return $this->redirectToRoute('overworld');
    }

}

?>