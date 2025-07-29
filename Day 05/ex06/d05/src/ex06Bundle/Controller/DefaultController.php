<?php

namespace ex06Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use ex06Bundle\Form\UserType;

class DefaultController extends Controller
{
    /**
     * @Route("/ex06", name="ex06_index")
     */
    public function indexAction()
    {
        return $this->render('ex06Bundle:Default:index.html.twig');
    }

    /**
     * @Route("/ex06/create-table", name="ex06_create_table")
     */
    public function createTableAction()
    {
        $query = "CREATE TABLE IF NOT EXISTS users_ex06 (
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
            return $this->render('ex06Bundle:Default:create_table.html.twig', [
                'success' => true, 
                'message' => 'Table created successfully!'
            ]);
        }
        catch (\Exception $e)
        {
            return $this->render('ex06Bundle:Default:create_table.html.twig', [
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/ex06/form", name="ex06_form")
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
            $query = "INSERT INTO users_ex06 (username, name, email, enable, birthdate, address) 
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
                return $this->redirectToRoute('ex06_list');
            }
            catch (\Exception $e)
            {
                $form->addError(new FormError('Error saving data: ' . $e->getMessage()));
            }
        }
        return $this->render('ex06Bundle:Default:form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/ex06/list", name="ex06_list")
     */
    public function listAction()
    {
        $connection = $this->getDoctrine()->getConnection();
        $query = "SELECT * FROM users_ex06 ORDER BY id DESC";
        
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
        return $this->render('ex06Bundle:Default:list.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/ex06/delete/{id}", name="ex06_delete")
     */
    public function deleteAction($id)
    {
        $connection = $this->getDoctrine()->getConnection();
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid user ID.');
            return $this->redirectToRoute('ex06_list');
        }
        $checkQuery = "SELECT COUNT(*) FROM users_ex06 WHERE id = ?";
        $count = $connection->fetchColumn($checkQuery, [$id]);
        if ($count == 0)
        {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('ex06_list');
        }
        $query = "DELETE FROM users_ex06 WHERE id = ?";
        try
        {
            $connection->executeQuery($query, [$id]);
            $this->addFlash('success', 'User deleted successfully!');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error deleting user: ' . $e->getMessage());
        }
        return $this->redirectToRoute('ex06_list');
    }

    /**
     * @Route("/ex06/update/{id}", name="ex06_update")
     */
    public function updateAction(Request $request, $id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid user ID.');
            return $this->redirectToRoute('ex06_list');
        }
        $connection = $this->getDoctrine()->getConnection();
        $checkQuery = "SELECT COUNT(*) FROM users_ex06 WHERE id = ?";
        $count = $connection->fetchColumn($checkQuery, [$id]);
        
        if ($count == 0)
        {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('ex06_list');
        }
        $query = "SELECT * FROM users_ex06 WHERE id = ?";
        $userData = $connection->fetchAssoc($query, [$id]);
        
        if (!$userData)
            throw $this->createNotFoundException('User not found');
        if ($userData['birthdate'])
            $userData['birthdate'] = new \DateTime($userData['birthdate']);
        $userData['enable'] = (bool)$userData['enable'];
        $form = $this->createForm(UserType::class, $userData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $birthdate = $data['birthdate'] ? $data['birthdate']->format('Y-m-d H:i:s') : null;
            $updateQuery = "UPDATE users_ex06 SET 
                            username = ?, 
                            name = ?, 
                            email = ?, 
                            enable = ?, 
                            birthdate = ?, 
                            address = ? 
                            WHERE id = ?";

            try
            {
                $connection->executeQuery($updateQuery, [
                    $data['username'],
                    $data['name'],
                    $data['email'],
                    $data['enable'] ? 1 : 0,
                    $birthdate,
                    $data['address'],
                    $id
                ]);

                $this->addFlash('success', 'User updated successfully!');
                return $this->redirectToRoute('ex06_list');
            }
            catch (\Exception $e)
            {
                $form->addError(new FormError('Error updating data: ' . $e->getMessage()));
            }
        }
        
        return $this->render('ex06Bundle:Default:update.html.twig', [
            'form' => $form->createView(),
            'user' => $userData
        ]);
    }

}