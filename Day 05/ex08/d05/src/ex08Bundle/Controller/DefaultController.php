<?php

namespace ex08Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use ex08Bundle\Form\UserType;

class DefaultController extends Controller
{
    /**
     * @Route("/ex08", name="ex08_index")
     */
    public function indexAction()
    {
        return $this->render('ex08Bundle:Default:index.html.twig');
    }

    /**
     * @Route("/ex08/create-table", name="ex08_create_table")
     */
    public function createTableAction()
    {
        $query = "CREATE TABLE IF NOT EXISTS users_ex08 (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            enable BOOLEAN NOT NULL DEFAULT FALSE,
            birthdate DATETIME
        )";
        
        try
        {
            $connection = $this->getDoctrine()->getConnection();
            $connection->executeQuery($query);
            return $this->render('ex08Bundle:Default:create_table.html.twig', [
                'success' => true, 
                'message' => 'Table created successfully!'
            ]);
        }
        catch (\Exception $e)
        {
            return $this->render('ex08Bundle:Default:create_table.html.twig', [
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/ex08/form", name="ex08_form")
     */
    public function formAction(Request $request)
    {
        $connection = $this->getDoctrine()->getConnection();
        $hasMaritalStatus = false;
        try
        {
            $checkQuery = "SHOW COLUMNS FROM users_ex08 LIKE 'marital_status'";
            $result = $connection->executeQuery($checkQuery);
            $hasMaritalStatus = $result->rowCount() > 0;
        }
        catch (\Exception $e) 
        {}  
        $form = $this->createForm(UserType::class, null, [
            'has_marital_status' => $hasMaritalStatus
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $birthdate = $data['birthdate'] ? $data['birthdate']->format('Y-m-d H:i:s') : null;
            
            if ($hasMaritalStatus && isset($data['maritalStatus'])) {
                $maritalStatus = $data['maritalStatus'];
                $query = "INSERT INTO users_ex08 (username, name, email, enable, birthdate, marital_status) 
                          VALUES (?, ?, ?, ?, ?, ?)
                          ON DUPLICATE KEY UPDATE 
                          name = VALUES(name),
                          enable = VALUES(enable),
                          birthdate = VALUES(birthdate),
                          marital_status = VALUES(marital_status)";
                $params = [
                    $data['username'],
                    $data['name'],
                    $data['email'],
                    $data['enable'] ? 1 : 0,
                    $birthdate,
                    $maritalStatus
                ];
            } else {
                $query = "INSERT INTO users_ex08 (username, name, email, enable, birthdate) 
                          VALUES (?, ?, ?, ?, ?)
                          ON DUPLICATE KEY UPDATE 
                          name = VALUES(name),
                          enable = VALUES(enable),
                          birthdate = VALUES(birthdate)";
                $params = [
                    $data['username'],
                    $data['name'],
                    $data['email'],
                    $data['enable'] ? 1 : 0,
                    $birthdate
                ];
            }
            
            try
            {
                $connection->executeQuery($query, $params);
                $this->addFlash('success', 'User saved successfully! (Created or updated if already existed)');
                return $this->redirectToRoute('ex08_list');
            }
            catch (\Exception $e)
            {
                $form->addError(new FormError('Error saving data: ' . $e->getMessage()));
            }
        }
        return $this->render('ex08Bundle:Default:form.html.twig', [
            'form' => $form->createView(),
            'has_marital_status' => $hasMaritalStatus
        ]);
    }

    /**
     * @Route("/ex08/list", name="ex08_list")
     */
    public function listAction()
    {
        $connection = $this->getDoctrine()->getConnection();
        $query = "SELECT * FROM users_ex08 ORDER BY id DESC";
        $hasMaritalStatus = false;
        try
        {
            $checkQuery = "SHOW COLUMNS FROM users_ex08 LIKE 'marital_status'";
            $result = $connection->executeQuery($checkQuery);
            $hasMaritalStatus = $result->rowCount() > 0;
        }
        catch (\Exception $e)
        {}
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
        return $this->render('ex08Bundle:Default:list.html.twig', [
            'users' => $users,
            'has_marital_status' => $hasMaritalStatus
        ]);
    }

    /**
     * @Route("/ex08/delete/{id}", name="ex08_delete")
     */
    public function deleteAction($id)
    {
        $connection = $this->getDoctrine()->getConnection();
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid user ID.');
            return $this->redirectToRoute('ex08_list');
        }
        $checkQuery = "SELECT COUNT(*) FROM users_ex08 WHERE id = ?";
        $count = $connection->fetchColumn($checkQuery, [$id]);
        if ($count == 0)
        {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('ex08_list');
        }
        $query = "DELETE FROM users_ex08 WHERE id = ?";
        try
        {
            $connection->executeQuery($query, [$id]);
            $this->addFlash('success', 'User deleted successfully!');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error deleting user: ' . $e->getMessage());
        }
        return $this->redirectToRoute('ex08_list');
    }

    /**
     * @Route("/ex08/update/{id}", name="ex08_update")
     */
    public function updateAction(Request $request, $id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid user ID.');
            return $this->redirectToRoute('ex08_list');
        }
        $connection = $this->getDoctrine()->getConnection();
        $hasMaritalStatus = false;
        try
        {
            $checkQuery = "SHOW COLUMNS FROM users_ex08 LIKE 'marital_status'";
            $result = $connection->executeQuery($checkQuery);
            $hasMaritalStatus = $result->rowCount() > 0;
        }
        catch (\Exception $e)
        {}
        
        $checkQuery = "SELECT COUNT(*) FROM users_ex08 WHERE id = ?";
        $count = $connection->fetchColumn($checkQuery, [$id]);
        
        if ($count == 0)
        {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('ex08_list');
        }
        $query = "SELECT * FROM users_ex08 WHERE id = ?";
        $userData = $connection->fetchAssoc($query, [$id]);
        
        if (!$userData)
            throw $this->createNotFoundException('User not found');
        if ($userData['birthdate'])
            $userData['birthdate'] = new \DateTime($userData['birthdate']);
        $userData['enable'] = (bool)$userData['enable'];
        
        $form = $this->createForm(UserType::class, $userData, [
            'has_marital_status' => $hasMaritalStatus
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $birthdate = $data['birthdate'] ? $data['birthdate']->format('Y-m-d H:i:s') : null;
            
            if ($hasMaritalStatus && isset($data['maritalStatus'])) {
                $updateQuery = "UPDATE users_ex08 SET 
                                username = ?, 
                                name = ?, 
                                email = ?, 
                                enable = ?, 
                                birthdate = ?,
                                marital_status = ?
                                WHERE id = ?";
                $params = [
                    $data['username'],
                    $data['name'],
                    $data['email'],
                    $data['enable'] ? 1 : 0,
                    $birthdate,
                    $data['maritalStatus'],
                    $id
                ];
            } else {
                $updateQuery = "UPDATE users_ex08 SET 
                                username = ?, 
                                name = ?, 
                                email = ?, 
                                enable = ?, 
                                birthdate = ?
                                WHERE id = ?";
                $params = [
                    $data['username'],
                    $data['name'],
                    $data['email'],
                    $data['enable'] ? 1 : 0,
                    $birthdate,
                    $id
                ];
            }

            try
            {
                $connection->executeQuery($updateQuery, $params);
                $this->addFlash('success', 'User updated successfully!');
                return $this->redirectToRoute('ex08_list');
            }
            catch (\Exception $e)
            {
                $form->addError(new FormError('Error updating data: ' . $e->getMessage()));
            }
        }
        return $this->render('ex08Bundle:Default:update.html.twig', [
            'form' => $form->createView(),
            'user' => $userData,
            'has_marital_status' => $hasMaritalStatus
        ]);
    }

    /**
     * @Route("/ex08/add-marital-status", name="ex08_add_marital_status")
     */
    public function addMaritalStatusAction()
    {
        $connection = $this->getDoctrine()->getConnection();
        $query = "ALTER TABLE users_ex08 ADD COLUMN marital_status ENUM('single', 'married', 'widower') DEFAULT 'single'";
        $checkQuery = "SHOW COLUMNS FROM users_ex08 LIKE 'marital_status'";
        $result = $connection->executeQuery($checkQuery);
        if ($result->rowCount() > 0)
        {
            $this->addFlash('warning', 'Marital status column already exists!');
            return $this->redirectToRoute('ex08_list');
        }
        try
        {
            $connection->executeQuery($query);
            $this->addFlash('success', 'Marital status column added successfully!');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error adding marital status column: ' . $e->getMessage());
        }
        return $this->redirectToRoute('ex08_list');
    }

    /**
     * @Route("/ex08/create-related-tables", name="ex08_create_related_tables")
     */
    public function createRelatedTablesAction()
    {
        $connection = $this->getDoctrine()->getConnection();
        $bankAccountsQuery = "CREATE TABLE IF NOT EXISTS bank_accounts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            account_number VARCHAR(50) NOT NULL UNIQUE,
            balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )";
        $addressesQuery = "CREATE TABLE IF NOT EXISTS addresses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            street VARCHAR(255) NOT NULL,
            city VARCHAR(100) NOT NULL,
            postal_code VARCHAR(20) NOT NULL,
            country VARCHAR(100) NOT NULL,
            address_type ENUM('home', 'work', 'other') NOT NULL DEFAULT 'home'
        )";

        try
        {
            $connection->executeQuery($bankAccountsQuery);
            $connection->executeQuery($addressesQuery);
            $this->addFlash('success', 'Related tables created successfully!');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error creating related tables: ' . $e->getMessage());
        }
        return $this->redirectToRoute('ex08_list');
    }

    /**
     * @Route("/ex08/create-relationships", name="ex08_create_relationships")
     */
    public function createRelationshipsAction()
    {
        $connection = $this->getDoctrine()->getConnection();
        
        try
        {
            try
            {
                $connection->executeQuery("ALTER TABLE bank_accounts DROP FOREIGN KEY fk_bank_user");
            }
            catch (\Exception $e)
            {}
            try
            {
                $connection->executeQuery("ALTER TABLE bank_accounts DROP INDEX unique_user_account");
            }
            catch (\Exception $e)
            {}
            try
            {
                $connection->executeQuery("ALTER TABLE addresses DROP FOREIGN KEY fk_address_user");
            }
            catch (\Exception $e)
            {}
            $bankAccountsQuery = "ALTER TABLE bank_accounts 
                ADD CONSTRAINT fk_bank_user FOREIGN KEY (user_id) REFERENCES users_ex08(id)";
            $bankAccountsUniqueQuery = "ALTER TABLE bank_accounts
                ADD UNIQUE INDEX unique_user_account (user_id)";
            $addressesQuery = "ALTER TABLE addresses 
                ADD CONSTRAINT fk_address_user FOREIGN KEY (user_id) REFERENCES users_ex08(id)";
            
            $connection->executeQuery($bankAccountsQuery);
            $connection->executeQuery($bankAccountsUniqueQuery);
            $connection->executeQuery($addressesQuery);
            $this->addFlash('success', 'Relationships created successfully!');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error creating relationships: ' . $e->getMessage());
        }
        return $this->redirectToRoute('ex08_list');
    }

}