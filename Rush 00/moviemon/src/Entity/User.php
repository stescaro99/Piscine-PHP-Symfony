<?php

namespace App\Entity;

use App\Entity\Moviemon;

class User
{
    private string $name;
    private int $health = 100;
    private int $strength = 10;
    private array $position = [2, 2];
    private int $mapSize = 6;
    private int $max_health = 100;

    /** @var Moviemon[] */
    private array $capturedMoviemons = [];

    /** @var Moviemon[] */
    private array $remainingMoviemons = [];

    // Constructor initializing $name
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    // --- Getters & Setters ---

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    public function setHealth(int $health): void
    {
        $this->health = $health;
    }

    public function getStrength(): int
    {
        return $this->strength;
    }

    public function setStrength(int $strength): void
    {
        $this->strength = $strength;
    }

    public function getPosition(): array
    {
        return $this->position;
    }

    public function setPosition(array $position): void
    {
        $this->position = $position;
    }

    public function getMapSize(): int
    {
        return $this->mapSize;
    }

    public function setMapSize(int $mapSize): void
    {
        $this->mapSize = $mapSize;
    }

    public function getMaxHealth(): int
    {
        return $this->max_health;
    }

    public function setMaxHealth(int $max_health): void
    {
        $this->max_health = $max_health;
    }

    /**
     * @return Moviemon[]
     */
    public function getCapturedMoviemons(): array
    {
        return $this->capturedMoviemons;
    }

    /**
     * @param Moviemon[] $capturedMoviemons
     */
    public function setCapturedMoviemons(array $capturedMoviemons): void
    {
        $this->capturedMoviemons = $capturedMoviemons;
    }

    /**
     * @return Moviemon[]
     */
    public function getRemainingMoviemons(): array
    {
        return $this->remainingMoviemons;
    }

    /**
     * @param Moviemon[] $remainingMoviemons
     */
    public function setRemainingMoviemons(array $remainingMoviemons): void
    {
        $this->remainingMoviemons = $remainingMoviemons;
    }

    /**
     * @param Moviemon[] $capturedMoviemons
     */
    public function defeatMoviemon(Moviemon $moviemon): void
    {
        $this->capturedMoviemons[] = $moviemon;
        $this->gainHealthAndStrength($moviemon);
        $this->removeRemainingMoviemon($moviemon);
    }

    /**
     * @param Moviemon[] $remainingMoviemons
     */
    private function removeRemainingMoviemon(Moviemon $moviemon): void
    {
        foreach ($this->remainingMoviemons as $key => $remainingMoviemon)
        {
            if ($remainingMoviemon->getName() === $moviemon->getName())
            {
                unset($this->remainingMoviemons[$key]);
                break;
            }
        }
    }

    private function gainHealthAndStrength(Moviemon $moviemon): void
    {
        $this->max_health += $this->max_health / 10;
        $this->health = $this->max_health;
        $this->strength += $moviemon->getStrength() / 10;
    }

    public function hasWon(): bool
    {
        return count($this->remainingMoviemons) === 0;
    }
}