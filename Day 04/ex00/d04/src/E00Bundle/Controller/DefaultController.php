<?php

namespace E00Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/e00/firstpage", name="e00_index")
     */
    public function indexAction()
    {
        return new Response('Hello world!');
    }
}
