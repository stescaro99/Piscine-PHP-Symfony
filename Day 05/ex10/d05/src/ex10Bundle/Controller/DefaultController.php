<?php

namespace ex10Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use ex10Bundle\Entity\Person;
use ex10Bundle\Form\PersonType;

class DefaultController extends Controller
{
    /**
     * @Route("/ex10/create-raw-table", name="ex10_create_raw_table")
     */
    public function createRawTableAction()
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $sql = "CREATE TABLE IF NOT EXISTS raw_persons_ex10 (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            enable BOOLEAN DEFAULT FALSE
        )";
        try {
            $connection->executeQuery($sql);
            $message = 'Raw SQL table created successfully!';
            $success = true;
        } catch (\Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $success = false;
        }
        return $this->render('ex10Bundle:Default:create_table.html.twig', [
            'message' => $message,
            'success' => $success
        ]);
    }

    /**
     * @Route("/ex10/import-file", name="ex10_import_file")
     */
    public function importFileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $filePath = $request->query->get('file', __DIR__.'/../Resources/import.txt');
        $messages = [];
        if (!file_exists($filePath))
        {
            $messages[] = 'File not found: ' . $filePath;
        }
        else
        {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $parts = explode(',', $line);
                if (count($parts) < 4) continue;
                list($username, $name, $email, $enable) = $parts;
                $enable = trim($enable);
                if ($enable === '' || strtolower($enable) === 'false' || $enable === '0')
                    $enableInt = 0;
                else
                    $enableInt = 1;
                $sql = 'INSERT INTO raw_persons_ex10 (username, name, email, enable) VALUES (?, ?, ?, ?)';
                $connection->executeQuery($sql, [$username, $name, $email, $enableInt]);
                $person = new Person();
                $person->setUsername($username);
                $person->setName($name);
                $person->setEmail($email);
                $person->setEnable((bool)$enableInt);
                $em->persist($person);
            }
            $em->flush();
            $messages[] = 'Import completed.';
        }
        return $this->render('ex10Bundle:Default:create_table.html.twig', [
            'message' => implode('<br>', $messages),
            'success' => true
        ]);
    }
    /**
     * @Route("/ex10", name="ex10_index")
     */
    public function indexAction()
    {
        return $this->render('ex10Bundle:Default:index.html.twig');
    }

    /**
     * @Route("/ex10/create-table", name="ex10_create_table")
     */
    public function createTableAction()
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
            $classMetadata = $em->getClassMetadata(Person::class);
            $schemaTool->updateSchema([$classMetadata], true);
            return $this->render('ex10Bundle:Default:create_table.html.twig', [
                'message' => 'Table created successfully! You must now create the related entities and relationships.',
                'success' => true
            ]);
        }
        catch (\Exception $e)
        {
            return $this->render('ex10Bundle:Default:create_table.html.twig', [
                'message' => 'Error: ' . $e->getMessage(),
                'success' => false
            ]);
        }
    }

    /**
     * @Route("/ex10/form", name="ex10_form")
     */
    public function formAction(Request $request)
    {
        $person = new Person();
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $schemaManager = method_exists($connection, 'createSchemaManager') ? $connection->createSchemaManager() : $connection->getSchemaManager();
        $columns = $schemaManager->listTableColumns('persons_ex10');
        $hasMaritalStatus = array_key_exists('marital_status', $columns);

        $form = $this->createForm(PersonType::class, $person, [
            'has_marital_status' => $hasMaritalStatus
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($person);
                $em->flush();

                $this->addFlash('success', 'Person saved successfully! (Created or updated if already existed)');
                return $this->redirectToRoute('ex10_list');
            }
            catch (\Exception $e)
            {
                $this->addFlash('error', 'Error saving person: ' . $e->getMessage());
            }
        }

        return $this->render('ex10Bundle:Default:form.html.twig', [
            'form' => $form->createView(),
            'has_marital_status' => $hasMaritalStatus
        ]);
    }

    /**
     * @Route("/ex10/list", name="ex10_list")
     */
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository(Person::class);
        $persons = $repository->findAll();
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $schemaManager = method_exists($connection, 'createSchemaManager') ? $connection->createSchemaManager() : $connection->getSchemaManager();
        $columns = $schemaManager->listTableColumns('persons_ex10');
        $hasMaritalStatus = array_key_exists('marital_status', $columns);

        $rawPersons = [];
        try
        {
            $rawPersons = $connection->fetchAll('SELECT * FROM raw_persons_ex10');
        }
        catch (\Exception $e)
        {
        }
        return $this->render('ex10Bundle:Default:list.html.twig', [
            'persons' => $persons,
            'raw_persons' => $rawPersons,
            'has_marital_status' => $hasMaritalStatus
        ]);
    }

    /**
     * @Route("/ex10/delete/{id}", name="ex10_delete")
     */
    public function deleteAction($id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid person ID.');
            return $this->redirectToRoute('ex10_list');
        }
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository(Person::class)->find($id);
        if (!$person)
        {
            $this->addFlash('error', 'Person not found.');
            return $this->redirectToRoute('ex10_list');
        }
        try
        {
            $em->remove($person);
            $em->flush();
            $this->addFlash('success', 'Person deleted successfully!');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error deleting person: ' . $e->getMessage());
        }
        return $this->redirectToRoute('ex10_list');
    }

    /**
     * @Route("/ex10/update/{id}", name="ex10_update")
     */
    public function updateAction(Request $request, $id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid person ID.');
            return $this->redirectToRoute('ex10_list');
        }
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository(Person::class)->find($id);
        $connection = $em->getConnection();
        $schemaManager = method_exists($connection, 'createSchemaManager') ? $connection->createSchemaManager() : $connection->getSchemaManager();
        $columns = $schemaManager->listTableColumns('persons_ex10');
        $hasMaritalStatus = array_key_exists('marital_status', $columns);

        if (!$person)
        {
            $this->addFlash('error', 'Person not found.');
            return $this->redirectToRoute('ex10_list');
        }
        $form = $this->createForm(PersonType::class, $person, [
            'has_marital_status' => $hasMaritalStatus
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            try
            {
                $em->flush();
                $this->addFlash('success', 'Person updated successfully!');
                return $this->redirectToRoute('ex10_list');
            }
            catch (\Exception $e)
            {
                $this->addFlash('error', 'Error updating person: ' . $e->getMessage());
            }
        }
        return $this->render('ex10Bundle:Default:update.html.twig', [
            'form' => $form->createView(),
            'person' => $person,
            'has_marital_status' => $hasMaritalStatus
        ]);
    }

     /**
     * @Route("/ex10/add-marital-status", name="ex10_add_marital_status")
     */
    public function addMaritalStatusAction()
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $schemaManager = method_exists($connection, 'createSchemaManager') ? $connection->createSchemaManager() : $connection->getSchemaManager();
        $columns = $schemaManager->listTableColumns('persons_ex10');
        $hasColumn = array_key_exists('marital_status', $columns);
        $personReflection = new \ReflectionClass(\ex10Bundle\Entity\Person::class);
        $hasProperty = $personReflection->hasProperty('maritalStatus');

        if (!$hasProperty) {
            $this->addFlash('error', "La proprietà 'maritalStatus' non esiste nell'entità Person. Aggiungila prima di aggiornare lo schema e assicurati di avere le annotazioni Doctrine.");
        } elseif ($hasColumn) {
            $this->addFlash('info', "La colonna 'marital_status' esiste già nel database.");
        } else {
            try {
                $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
                $classMetadata = $em->getClassMetadata(\ex10Bundle\Entity\Person::class);
                $schemaTool->updateSchema([$classMetadata], true);
                $this->addFlash('success', "Colonna 'marital_status' aggiunta con successo tramite Doctrine ORM!");
            } catch (\Exception $e) {
                $this->addFlash('error', "Errore nell'aggiornamento dello schema: " . $e->getMessage());
            }
        }
        return $this->redirectToRoute('ex10_list');
    }

    /**
     * @Route("/ex10/create-related-entities", name="ex10_create_related_entities")
     */
    public function createRelatedEntitiesAction()
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
            $classes = $em->getMetadataFactory()->getAllMetadata();
            $schemaTool->updateSchema($classes, true);
            
            $this->addFlash('success', 'Related entities tables created/updated successfully! (BankAccount, Address)');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error creating related entities: ' . $e->getMessage());
        }

        return $this->redirectToRoute('ex10_list');
    }

    /**
     * @Route("/ex10/create-relationships", name="ex10_create_relationships")
     */
    public function createRelationshipsAction()
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
            $classes = $em->getMetadataFactory()->getAllMetadata();
            
            $schemaTool->updateSchema($classes, true);
            $this->addFlash('success', 'Relationships created successfully! Check your entities for @OneToOne and @OneToMany annotations.');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error creating relationships: ' . $e->getMessage());
        }
        return $this->redirectToRoute('ex10_list');
    }

}