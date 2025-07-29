<?php

namespace ex02Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use ex02Bundle\Form\UserType;

class DefaultController extends Controller
{
    /**
     * @Route("/ex02", name="ex02_index")
     */
    public function indexAction()
    {
        return $this->render('ex02Bundle:Default:index.html.twig');
    }

    /**
     * @Route("/ex02/create-table", name="ex02_create_table")
     */
    public function createTableAction()
    {
        $query = "CREATE TABLE IF NOT EXISTS users_ex02 (
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
            return $this->render('ex02Bundle:Default:create_table.html.twig', [
                'success' => true, 
                'message' => 'Table created successfully!'
            ]);
        }
        catch (\Exception $e)
        {
            return $this->render('ex02Bundle:Default:create_table.html.twig', [
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/ex02/form", name="ex02_form")
     */
    public function formAction(Request $request)
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $connection = $this->getDoctrine()->getConnection();
            $birthdate = $data['birthdate'] ? $data['birthdate']->format('Y-m-d H:i:s') : null;
            $query = "INSERT INTO users_ex02 (username, name, email, enable, birthdate, address) 
                      VALUES (?, ?, ?, ?, ?, ?)
                      ON DUPLICATE KEY UPDATE 
                      name = VALUES(name),
                      enable = VALUES(enable),
                      birthdate = VALUES(birthdate),
                      address = VALUES(address)";
            
            try
            {
                $connection->executeQuery($query, [
                    $data['username'],
                    $data['name'],
                    $data['email'],
                    $data['enable'] ? 1 : 0,
                    $birthdate,
                    $data['address']
                ]);
                
                $this->addFlash('success', 'User saved successfully! (Created or updated if already existed)');
                return $this->redirectToRoute('ex02_list');
            }
            catch (\Exception $e)
            {
                $form->addError(new FormError('Error saving data: ' . $e->getMessage()));
            }
        }
        return $this->render('ex02Bundle:Default:form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/ex02/list", name="ex02_list")
     */
    public function listAction()
    {
        $connection = $this->getDoctrine()->getConnection();
        $query = "SELECT * FROM users_ex02 ORDER BY id DESC";
        
        try
        {
            $result = $connection->executeQuery($query);
            $users = $result->fetchAll();
        }
        catch (\Exception $e)
        {
            $users = [];
            $this->addFlash('error', 'Error fetching data: ' . $e->getMessage());
        }
        return $this->render('ex02Bundle:Default:list.html.twig', ['users' => $users]);
    }
}