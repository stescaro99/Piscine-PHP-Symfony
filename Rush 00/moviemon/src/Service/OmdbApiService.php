<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Moviemon;


class OmdbApiService
{
    private HttpClientInterface $httpClient;
    private $logger;

    public function __construct(HttpClientInterface $httpClient, \Psr\Log\LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->apiKey = $_ENV['API_KEY'];
    }

    private function empty_url(string $url): bool
    {
        if (!$url || $url === 'N/A')
            return true;
        if (str_starts_with($url, 'http'))
        {
            try
            {
                $headers = @get_headers($url);
                if ($headers && strpos($headers[0], '200') !== false)
                    return false;
            }
            catch (\Throwable $e)
            {
            }
            return true;
        }
        return false;
    }

    private function fetchMoviemonById(string $id): ?Moviemon
    {
        $url = 'https://www.omdbapi.com/?i=tt' . urlencode($id) . '&apikey=' . $this->apiKey;
        try
        {
            $response = $this->httpClient->request('GET', $url);
            $data = $response->toArray();
        }
        catch (\Throwable $e)
        {
            $this->logger->error('Error fetching data from OMDB API: ' . $e->getMessage());
            return null;
        }
        $moviemon = new Moviemon();
        $moviemon->setName($data['Title'] ?? 'Unknown');
        $imdbRating = is_numeric($data['imdbRating'] ?? null) ? (float)$data['imdbRating'] : 5.0;
        $metascore = is_numeric($data['Metascore'] ?? null) ? (int)$data['Metascore'] : 50;
        $moviemon->setHealth(round($imdbRating * 10) ?? 10);
        $moviemon->setMaxHealth($moviemon->getHealth());
        $moviemon->setStrength(round($metascore) ?? 10);
        $posterUrl = $data['Poster'] ?? '/images/unknown_poster.png';
        if ($this->empty_url($posterUrl))
            $posterUrl = '/images/unknown_poster.png';
        $this->logger->info(($data['Title'] ?? 'Unknown') . ' URL: ' . $posterUrl);
        $moviemon->setUrlPoster($posterUrl);
        $moviemon->setPlot($data['Plot'] ?? 'Unknown plot');
        return $moviemon;
    }

    public function getMoviemonById(int $id): ?Moviemon
    {
        if ($id < 1 || $id > 2404811)
            {
                logger()->error('Invalid ID format: ' . $id);
                return null;
            }
            $paddedId = str_pad($id, 7, '0', STR_PAD_LEFT);
        return $this->fetchMoviemonById($paddedId);
    }

    public function populateMoviemon(array $movie_ids): array
    {
        $moviemons = [];
        foreach ($movie_ids as $id)
        {
            $moviemon = $this->getMoviemonById($id);
            if ($moviemon !== null)
                $moviemons[] = $moviemon;
        }
        return $moviemons;
    }

    public function getCatchableMoviemon(): array
    {
        $movie_ids = [];
        $id = random_int(1, 2404811);
        for ($i = 0; $i < 10; $i++)
        {
            while (in_array($id, $movie_ids))
                $id = random_int(1, 2404811);
            $movie_ids[$i] = $id;
        }
        return $movie_ids;
    }
}
