<?php

namespace ex00Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('ex00Bundle:Default:index.html.twig');
    }

    /**
     * @Route("/create-table", name="create_table")
     */
    public function createTableAction()
    {
        $query = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            enable BOOLEAN NOT NULL DEFAULT FALSE,
            birthdate DATETIME,
            address TEXT
        )";
        try
        {
            $connection = $this->getDoctrine()->getConnection();
            $connection->executeQuery($query);
            return $this->render('ex00Bundle:Default:create_table.html.twig', [
                'message' => 'Table created successfully!',
                'success' => true
            ]);
        }
        catch (\Exception $e)
        {
            return $this->render('ex00Bundle:Default:create_table.html.twig', [
                'message' => 'Error creating table: ' . $e->getMessage(),
                'success' => false
            ]);
        }
    }
}
