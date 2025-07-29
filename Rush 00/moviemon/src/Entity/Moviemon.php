<?php

namespace App\Entity;

class Moviemon
{
    private string $name;
    private int $health;
    private int $strength;
    private string $urlPoster;
    private string $plot;
    private int $maxHealth;

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

    public function getUrlPoster(): string
    {
        return $this->urlPoster;
    }
    public function setUrlPoster(string $urlPoster): void
    {
        $this->urlPoster = $urlPoster;
    }

    public function getPlot(): string
    {
        return $this->plot;
    }

    public function setPlot(string $plot): void
    {
        $this->plot = $plot;
    }

    public function getMaxHealth(): int
    {
        return $this->maxHealth;
    }
    
    public function setMaxHealth(int $maxHealth): void
    {
        $this->maxHealth = $maxHealth;
    }
}
