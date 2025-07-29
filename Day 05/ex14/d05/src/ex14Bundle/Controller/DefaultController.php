<?php

namespace ex14Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/ex14", name="ex14_index")
     */
    public function indexAction()
    {
        $connection = $this->get('database_connection');
        $tableExists = false;
        try {
            $result = $connection->fetchAll("SHOW TABLES LIKE 'injections_ex14'");
            $tableExists = count($result) > 0;
        } catch (\Exception $e) {
            $tableExists = false;
        }
        return $this->render('ex14Bundle:Default:index.html.twig', [
            'tableExists' => $tableExists
        ]);
    }

    /**
     * @Route("/ex14/create-table", name="ex14_create_table")
     */
    public function createTableAction()
    {
        $connection = $this->get('database_connection');
        try {
            $connection->executeQuery("CREATE TABLE IF NOT EXISTS injections_ex14 (id INT AUTO_INCREMENT PRIMARY KEY, info VARCHAR(255))");
            $message = 'Table created or already exists.';
        } catch (\Exception $e) {
            $message = 'Error creating table: ' . $e->getMessage();
        }
        return $this->redirectToRoute('ex14_index');
    }

    /**
     * @Route("/ex14/form", name="ex14_form")
     */
    public function formAction(Request $request)
    {
        $connection = $this->get('database_connection');
        $error = '';
        if ($request->isMethod('POST')) {
            $info = $request->request->get('info', '');
            try {
                // VULNERABLE SQL: no validation, direct injection
                $connection->executeQuery("INSERT INTO injections_ex14 (info) VALUES ('$info')");
                $this->addFlash('success', 'Info saved!');
                return $this->redirectToRoute('ex14_index');
            } catch (\Exception $e) {
                $error = 'Error: ' . $e->getMessage();
            }
        }
        return $this->render('ex14Bundle:Default:form.html.twig', [
            'error' => $error
        ]);
    }

    /**
     * @Route("/ex14/list", name="ex14_list")
     */
    public function listAction()
    {
        $connection = $this->get('database_connection');
        $tableExists = false;
        $results = [];
        try {
            $result = $connection->fetchAll("SHOW TABLES LIKE 'injections_ex14'");
            $tableExists = count($result) > 0;
            if ($tableExists) {
                $results = $connection->fetchAll("SELECT * FROM injections_ex14");
            }
        } catch (\Exception $e) {
            $tableExists = false;
        }
        return $this->render('ex14Bundle:Default:list.html.twig', [
            'results' => $results,
            'tableExists' => $tableExists
        ]);
    }
}