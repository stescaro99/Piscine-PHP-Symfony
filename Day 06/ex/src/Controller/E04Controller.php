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

class E04Controller extends AbstractController
{
    #[Route('/e04', name: 'e04homepage')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $session = $request->getSession();
        $anonymousName = null;
        $secondsSinceLastRequest = null;

        if (!$user)
        {
            $animals = ['dog', 'cat', 'fox', 'vixen', 'owl', 'bear', 'wolf', 'lion', 'tiger', 'rabbit', 'panda', 'elephant', 'giraffe', 'zebra', 'monkey', 'koala', 'kangaroo', 'hippo', 'rhino', 'crocodile', 'alligator', 'penguin', 'seal', 'dolphin', 'whale', 'shark', 'octopus', 'squid', 'jellyfish', 'starfish', 'crab', 'lobster', 'shrimp', 'clam', 'oyster', 'scallop', 'mussel', 'sea urchin', 'sea cucumber', 'sea anemone', 'coral', 'sea turtle', 'seahorse', 'manatee', 'dugong', 'narwhal', 'beluga', 'walrus', 'sea lion', 'stingray', 'manta ray', 'barracuda', 'pufferfish', 'lionfish', 'clownfish', 'angelfish', 'butterflyfish', 'parrotfish', 'surgeonfish', 'triggerfish', 'wrasse', 'grouper', 'snapper', 'tuna', 'marlin', 'swordfish', 'fly', 'dragonfly', 'butterfly', 'moth', 'beetle', 'ant', 'bee', 'wasp', 'hornet', 'grasshopper', 'cricket', 'locust', 'caterpillar', 'larva', 'pupa', 'nymph', 'roach', 'termite', 'firefly', 'lightning bug', 'mosquito', 'gnat', 'tick', 'flea', 'louse', 'bedbug', 'aphid', 'scale insect', 'mealybug', 'whitefly', 'leafhopper', 'planthopper', 'cicada', 'treehopper', 'spittlebug', 'froghopper', 'leafcutter ant', 'army ant', 'carpenter ant', 'fire ant', 'harvester ant', 'sugar ant', 'pavement ant', 'french person', 'pisano'];
            $now = time();
            $resetAnonymous = false;
            if ($session->has('last_request_time'))
            {
                $lastRequest = $session->get('last_request_time');
                $secondsSinceLastRequest = $now - $lastRequest;
                if ($secondsSinceLastRequest > 60)
                {
                    $session->remove('anonymous_name');
                    $resetAnonymous = true;
                }
            }
            if (!$session->has('anonymous_name') || $resetAnonymous)
            {
                $randomAnimal = $animals[array_rand($animals)];
                $session->set('anonymous_name', 'Anonymous ' . $randomAnimal);
            }
            $anonymousName = $session->get('anonymous_name');
            $session->set('last_request_time', $now);
        }

        return $this->render('homepages/e04.html.twig', [
            'user' => $user,
            'anonymous_name' => $anonymousName,
            'seconds_since_last_request' => $secondsSinceLastRequest,
        ]);
    }
}