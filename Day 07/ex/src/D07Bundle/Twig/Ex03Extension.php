<?php

namespace App\D07Bundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Ex03Extension extends AbstractExtension
{
    private $ex03Service;

    public function __construct(\App\D07Bundle\Service\Ex03Service $ex03Service)
    {
        $this->ex03Service = $ex03Service;
    }

    public function getFilters()
    {
        return [
            new \Twig\TwigFilter('uppercaseWords', [$this->ex03Service, 'uppercaseWords']),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('countNumbers', [$this->ex03Service, 'countNumbers']),
        ];
    }
}