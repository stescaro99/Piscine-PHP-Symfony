<?php

namespace ex01Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\ORM\Tools\SchemaTool;
use ex01Bundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * @Route("/e01", name="ex01_index")
     */
    public function indexAction()
    {
        return $this->render('ex01Bundle:Default:index.html.twig');
    }

    /**
     * @Route("/e01/create_table", name="ex01_create_table")
     */
    public function createTableAction()
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $schemaTool = new SchemaTool($em);
            $classes = array($em->getClassMetadata('ex01Bundle\Entity\User'));
            $schemaTool->updateSchema($classes, true);
            return $this->render('ex01Bundle:Default:create_table.html.twig', [
                'message' => 'Table created successfully!',
                'success' => true
            ]);
        }
        catch (\Exception $e)
        {
            return $this->render('ex01Bundle:Default:create_table.html.twig', [
                'message' => 'Error creating table: ' . $e->getMessage(),
                'success' => false
            ]);
        }
    }
}
