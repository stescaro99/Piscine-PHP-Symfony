<?php

namespace ex04Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use ex04Bundle\Form\UserType;

class DefaultController extends Controller
{
    /**
     * @Route("/ex04", name="ex04_index")
     */
    public function indexAction()
    {
        return $this->render('ex04Bundle:Default:index.html.twig');
    }

    /**
     * @Route("/ex04/create-table", name="ex04_create_table")
     */
    public function createTableAction()
    {
        $query = "CREATE TABLE IF NOT EXISTS users_ex04 (
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
            return $this->render('ex04Bundle:Default:create_table.html.twig', [
                'success' => true, 
                'message' => 'Table created successfully!'
            ]);
        }
        catch (\Exception $e)
        {
            return $this->render('ex04Bundle:Default:create_table.html.twig', [
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/ex04/form", name="ex04_form")
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
            $query = "INSERT INTO users_ex04 (username, name, email, enable, birthdate, address) 
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
                return $this->redirectToRoute('ex04_list');
            }
            catch (\Exception $e)
            {
                $form->addError(new FormError('Error saving data: ' . $e->getMessage()));
            }
        }
        return $this->render('ex04Bundle:Default:form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/ex04/list", name="ex04_list")
     */
    public function listAction()
    {
        $connection = $this->getDoctrine()->getConnection();
        $query = "SELECT * FROM users_ex04 ORDER BY id DESC";
        
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
        return $this->render('ex04Bundle:Default:list.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/ex04/delete/{id}", name="ex04_delete")
     */
    public function deleteAction($id)
    {
        $connection = $this->getDoctrine()->getConnection();
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid user ID.');
            return $this->redirectToRoute('ex04_list');
        }
        $checkQuery = "SELECT COUNT(*) FROM users_ex04 WHERE id = ?";
        $count = $connection->fetchColumn($checkQuery, [$id]);
        if ($count == 0)
        {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('ex04_list');
        }
        $query = "DELETE FROM users_ex04 WHERE id = ?";
        try
        {
            $connection->executeQuery($query, [$id]);
            $this->addFlash('success', 'User deleted successfully!');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error deleting user: ' . $e->getMessage());
        }
        return $this->redirectToRoute('ex04_list');
    }
}