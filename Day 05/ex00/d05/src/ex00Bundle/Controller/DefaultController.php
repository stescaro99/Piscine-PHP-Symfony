<?php

namespace ex00Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/ex00", name="ex00_index")
     */
    public function indexAction()
    {
        return $this->render('ex00Bundle:Default:index.html.twig');
    }

    /**
     * @Route("/ex00/create-table", name="ex00_create_table")
     */
    public function createTableAction()
    {
    }
}