<?php

namespace App\Service;

use App\Entity\User;
use App\Service\OmdbApiService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Moviemon;

class GameManager
{
    private $mapSize;
    private const SAVE_DIR = __DIR__ . '/../../var/saves/';
    private $session;
    private SerializerInterface $serializer;
    private Filesystem $filesystem;
    private OmdbApiService $omdb;

    public function __construct(
        RequestStack $requestStack,
        SerializerInterface $serializer,
        OmdbApiService $omdb
    ) {
        $this->session = $requestStack->getSession();
        $this->serializer = $serializer;
        $this->omdb = $omdb;
        $this->filesystem = new Filesystem();
        // $this->mapSize = [6, 6];
        $this->mapSize = $this->session->get('map_size') ?? 6;

        if (!$this->filesystem->exists(self::SAVE_DIR)) {
            $this->filesystem->mkdir(self::SAVE_DIR);
        }
    }

    public function startNewGame(string $username, int $mapSize): void
    {
        $user = new User(
            $username,
            100, // Default health
            10,  // Default strength
            0,   // Starting x position
            0    // Starting y position
        );

        $ids = $this->omdb->getCatchableMoviemon();
        $moviemons = $this->omdb->populateMoviemon($ids);

        $user->setMapSize($mapSize);
        $user->setRemainingMoviemons($moviemons);
        $user->setCapturedMoviemons([]);

        // $session = $request->getSession();
        echo $mapSize;
        // exit;
        $this->session->set('map_size', $mapSize);

        // $map = array($mapSize, $mapSize);
        // $this->session->set('map_size', $map);
        $this->session->set('user_name', $username);
        $this->saveUser($user);
    }

    public function saveGame(): void
    {
        $user = $this->getUser();
        if ($user) {
            $this->saveUser($user);
        }
    }

    public function loadGame(string $username): void
    {
        $filepath = self::SAVE_DIR . $username . '.json';
        if (!$this->filesystem->exists($filepath)) {
            throw new \Exception("Save file not found for $username");
        }

        $json = file_get_contents($filepath);
        $user = $this->serializer->deserialize($json, User::class, 'json');

        $this->session->set('user_name', $username);
        $this->session->set('user', $user);

        // Aggiorna la mapsize del GameManager e Session
        $this->mapSize = $user->getMapSize();
        $this->session->set('map_size', $this->mapSize);
    }

    public function resetGame(): void
    {
        $this->session->remove('user');
        $this->session->remove('user_name');
    }

    public function getUser(): ?User
    {
        return $this->session->get('user');
    }

    public function listSavedGames(): array
    {
        $files = scandir(self::SAVE_DIR);
        $saves = [];

        foreach ($files as $file) {
            if (str_ends_with($file, '.json')) {
                $saves[] = basename($file, '.json');
            }
        }

        return $saves;
    }

    private function saveUser(User $user): void
    {
        $filename = self::SAVE_DIR . $user->getName() . '.json';
        $json = $this->serializer->serialize($user, 'json');
        $this->filesystem->dumpFile($filename, $json);
        $this->session->set('user', $user);
    }

    public function rand_encounter(array $moviemons): ?Moviemon
    {
        $moviemons = array_values($moviemons);
        $n_moviemons = count($moviemons);
        if ($n_moviemons === 0)
            return null;
        $i = random_int(0, $n_moviemons - 1);

        if (random_int(1, 100) > 20)
            return null;
        return $moviemons[$i] ?? null;
    }

    public function getMoviemon(User $user, string $moviemonName): ?Moviemon
    {
        foreach ($user->getRemainingMoviemons() as $moviemon)
        {
            if ($moviemon->getName() === $moviemonName)
            {
                return $moviemon;
            }
        }
        return null;
    }

    public function setMapSize(int $mapSize): void
    {
        $this->mapSize = $mapSize;
        $this->session->set('map_size', $mapSize);
    }

    public function getMapSize(): int
    {
        return $this->mapSize;
    }

}