<?php

namespace E01Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    private $allowedCategories = [
        'controller', 'routing', 'templating', 'doctrine', 'testing',
        'validation', 'forms', 'security', 'cache', 'translations', 'services'
    ];

    /**
     * @Route("/e01", name="e01_index")
     */
    public function indexAction()
    {
        return $this->render('E01Bundle:Default:index.html.twig', [
            'categories' => $this->allowedCategories
        ]);
    }

    /**
     * @Route("/e01/{category}", name="e01_category")
     */
    public function categoryAction($category)
    {
        if (!in_array($category, $this->allowedCategories))
            return $this->redirectToRoute('e01_index');
        return $this->render('E01Bundle:Default:category.html.twig', [
            'category' => $category,
            'categories' => $this->allowedCategories
        ]);
    }
}
