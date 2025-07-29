<?php

namespace ex11Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use ex11Bundle\Entity\Person;
use ex11Bundle\Form\PersonType;

class DefaultController extends Controller
{
    /**
     * @Route("/ex11/reset-db", name="ex11_reset_db")
     */
    public function resetDbAction()
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        try
        {
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
            $tables = ['persons_ex11', 'raw_persons_ex11', 'bank_accounts_ex11', 'address_ex11'];
            foreach ($tables as $table)
            {
                try
                {
                    $connection->executeQuery('TRUNCATE TABLE ' . $table);
                }
                catch (\Exception $te)
                {
                }
            }
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
            $message = 'Database azzerato con successo!';
            $success = true;
        }
        catch (\Exception $e)
        {
            $message = 'Errore durante l\'azzeramento: ' . $e->getMessage();
            $success = false;
        }
        return $this->render('ex11Bundle:Default:create_table.html.twig', [
            'message' => $message,
            'success' => $success
        ]);
    }


    /**
     * @Route("/ex11", name="ex11_index")
     */
    public function indexAction()
    {
        return $this->render('ex11Bundle:Default:index.html.twig');
    }


    /**
     * @Route("/ex11/form", name="ex11_form")
     */
    public function formAction(Request $request)
    {
        $connection = $this->getDoctrine()->getManager()->getConnection();
        $error = '';
        if ($request->isMethod('POST'))
        {
            $username = trim($request->request->get('username', ''));
            $name = trim($request->request->get('name', ''));
            $email = trim($request->request->get('email', ''));
            $birthdate = trim($request->request->get('birthdate', ''));
            $enable = $request->request->get('enable', '0');
            if (!$username || !$name || !$email || !$birthdate)
            {
                $error = 'All fields are required.';
            }
            elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate))
            {
                $error = 'Birthdate must be in YYYY-MM-DD format.';
            }
            else
            {
                try
                {
                    $sql = 'INSERT INTO persons_ex11 (username, name, email, birthdate, enable) VALUES (?, ?, ?, ?, ?)';
                    $connection->executeQuery($sql, [$username, $name, $email, $birthdate, $enable ? 1 : 0]);
                    $this->addFlash('success', 'Person saved successfully!');
                    return $this->redirectToRoute('ex11_list');
                }
                catch (\Exception $e)
                {
                    $error = 'Error saving person: ' . $e->getMessage();
                }
            }
        }
        return $this->render('ex11Bundle:Default:form.html.twig', [
            'error' => $error
        ]);
    }

    /**
     * @Route("/ex11/list", name="ex11_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $request = $this->get('request_stack')->getCurrentRequest();
        $filterDate = $request->query->get('birthdate', '');
        $sortField = $request->query->get('sort', 'name');
        $sortDir = strtolower($request->query->get('dir', 'asc')) === 'desc' ? 'DESC' : 'ASC';

        $allowedSort = ['name', 'email', 'balance'];
        if (!in_array($sortField, $allowedSort))
            $sortField = 'name';
        if ($filterDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate))
            $filterDate = '';
        $schemaManager = method_exists($connection, 'createSchemaManager') ? $connection->createSchemaManager() : $connection->getSchemaManager();
        $columns = $schemaManager->listTableColumns('persons_ex11');
        $hasMaritalStatus = array_key_exists('marital_status', $columns);

        $sql = "SELECT p.id, p.name, p.email, p.birthdate, b.account_number, b.balance";
        if ($hasMaritalStatus)
        {
            $sql .= ", p.marital_status";
        }
        $sql .= " FROM persons_ex11 p
                LEFT JOIN bank_accounts_ex11 b ON p.id = b.person_id
                WHERE 1=1 ";
        $params = [];
        if ($filterDate)
        {
            $sql .= " AND p.birthdate >= ? ";
            $params[] = $filterDate;
        }
        $sql .= " ORDER BY " . ($sortField === 'balance' ? 'b.balance' : 'p.' . $sortField) . " $sortDir";
        $results = $connection->fetchAll($sql, $params);
        return $this->render('ex11Bundle:Default:list.html.twig', [
            'results' => $results,
            'filterDate' => $filterDate,
            'sortField' => $sortField,
            'sortDir' => $sortDir,
            'has_marital_status' => $hasMaritalStatus
        ]);
    }

    /**
     * @Route("/ex11/delete/{id}", name="ex11_delete")
     */
    public function deleteAction($id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid person ID.');
            return $this->redirectToRoute('ex11_list');
        }
        $connection = $this->getDoctrine()->getManager()->getConnection();
        try
        {
            $sql = 'DELETE FROM persons_ex11 WHERE id = ?';
            $connection->executeQuery($sql, [$id]);
            $this->addFlash('success', 'Person deleted successfully!');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error deleting person: ' . $e->getMessage());
        }
        return $this->redirectToRoute('ex11_list');
    }

    /**
     * @Route("/ex11/update/{id}", name="ex11_update")
     */
    public function updateAction(Request $request, $id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid person ID.');
            return $this->redirectToRoute('ex11_list');
        }
        $connection = $this->getDoctrine()->getManager()->getConnection();
        $error = '';
        $person = $connection->fetchAssociative('SELECT * FROM persons_ex11 WHERE id = ?', [$id]);
        if (!$person) {
            $this->addFlash('error', 'Person not found.');
            return $this->redirectToRoute('ex11_list');
        }
        if ($request->isMethod('POST'))
        {
            $username = trim($request->request->get('username', $person['username']));
            $name = trim($request->request->get('name', $person['name']));
            $email = trim($request->request->get('email', $person['email']));
            $birthdate = trim($request->request->get('birthdate', $person['birthdate']));
            $enable = $request->request->get('enable', $person['enable']);
            if (!$username || !$name || !$email || !$birthdate)
            {
                $error = 'All fields are required.';
            }
            elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate))
            {
                $error = 'Birthdate must be in YYYY-MM-DD format.';
            }
            else
            {
                try
                {
                    $sql = 'UPDATE persons_ex11 SET username = ?, name = ?, email = ?, birthdate = ?, enable = ? WHERE id = ?';
                    $connection->executeQuery($sql, [$username, $name, $email, $birthdate, $enable ? 1 : 0, $id]);
                    $this->addFlash('success', 'Person updated successfully!');
                    return $this->redirectToRoute('ex11_list');
                }
                catch (\Exception $e)
                {
                    $error = 'Error updating person: ' . $e->getMessage();
                }
            }
        }
        return $this->render('ex11Bundle:Default:update.html.twig', [
            'person' => $person,
            'error' => $error
        ]);
    }




}