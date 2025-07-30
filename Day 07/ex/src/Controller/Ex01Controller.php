<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Ex01Controller extends AbstractController
{
    #[Route('/ex01', name: 'ex01')]
    public function index(): Response
    {
        return $this->render('ex01/index.html.twig', ['number' => $this->getParameter('d07.number'), 'enable' => $this->getParameter('d07.enable')]);
    }
}