<?php

namespace ex09Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use ex09Bundle\Entity\Person;
use ex09Bundle\Form\PersonType;

class DefaultController extends Controller
{
    /**
     * @Route("/ex09", name="ex09_index")
     */
    public function indexAction()
    {
        return $this->render('ex09Bundle:Default:index.html.twig');
    }

    /**
     * @Route("/ex09/create-table", name="ex09_create_table")
     */
    public function createTableAction()
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
            $classMetadata = $em->getClassMetadata(Person::class);
            $schemaTool->updateSchema([$classMetadata], true);
            return $this->render('ex09Bundle:Default:create_table.html.twig', [
                'message' => 'Table created successfully! You must now create the related entities and relationships.',
                'success' => true
            ]);
        }
        catch (\Exception $e)
        {
            return $this->render('ex09Bundle:Default:create_table.html.twig', [
                'message' => 'Error: ' . $e->getMessage(),
                'success' => false
            ]);
        }
    }

    /**
     * @Route("/ex09/form", name="ex09_form")
     */
    public function formAction(Request $request)
    {
        $person = new Person();
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $schemaManager = method_exists($connection, 'createSchemaManager') ? $connection->createSchemaManager() : $connection->getSchemaManager();
        $columns = $schemaManager->listTableColumns('persons_ex09');
        $hasMaritalStatus = array_key_exists('marital_status', $columns);

        $form = $this->createForm(PersonType::class, $person, [
            'has_marital_status' => $hasMaritalStatus
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($person);
                $em->flush();

                $this->addFlash('success', 'Person saved successfully! (Created or updated if already existed)');
                return $this->redirectToRoute('ex09_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error saving person: ' . $e->getMessage());
            }
        }

        return $this->render('ex09Bundle:Default:form.html.twig', [
            'form' => $form->createView(),
            'has_marital_status' => $hasMaritalStatus
        ]);
    }

    /**
     * @Route("/ex09/list", name="ex09_list")
     */
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository(Person::class);
        $persons = $repository->findAll();
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $schemaManager = method_exists($connection, 'createSchemaManager') ? $connection->createSchemaManager() : $connection->getSchemaManager();
        $columns = $schemaManager->listTableColumns('persons_ex09');
        $hasMaritalStatus = array_key_exists('marital_status', $columns);

        return $this->render('ex09Bundle:Default:list.html.twig', [
            'persons' => $persons,
            'has_marital_status' => $hasMaritalStatus
        ]);
    }

    /**
     * @Route("/ex09/delete/{id}", name="ex09_delete")
     */
    public function deleteAction($id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid person ID.');
            return $this->redirectToRoute('ex09_list');
        }
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository(Person::class)->find($id);
        if (!$person)
        {
            $this->addFlash('error', 'Person not found.');
            return $this->redirectToRoute('ex09_list');
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
        return $this->redirectToRoute('ex09_list');
    }

    /**
     * @Route("/ex09/update/{id}", name="ex09_update")
     */
    public function updateAction(Request $request, $id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid person ID.');
            return $this->redirectToRoute('ex09_list');
        }
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository(Person::class)->find($id);
        $connection = $em->getConnection();
        $schemaManager = method_exists($connection, 'createSchemaManager') ? $connection->createSchemaManager() : $connection->getSchemaManager();
        $columns = $schemaManager->listTableColumns('persons_ex09');
        $hasMaritalStatus = array_key_exists('marital_status', $columns);

        if (!$person)
        {
            $this->addFlash('error', 'Person not found.');
            return $this->redirectToRoute('ex09_list');
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
                return $this->redirectToRoute('ex09_list');
            }
            catch (\Exception $e)
            {
                $this->addFlash('error', 'Error updating person: ' . $e->getMessage());
            }
        }
        return $this->render('ex09Bundle:Default:update.html.twig', [
            'form' => $form->createView(),
            'person' => $person,
            'has_marital_status' => $hasMaritalStatus
        ]);
    }

     /**
     * @Route("/ex09/add-marital-status", name="ex09_add_marital_status")
     */
    public function addMaritalStatusAction()
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $schemaManager = method_exists($connection, 'createSchemaManager') ? $connection->createSchemaManager() : $connection->getSchemaManager();
        $columns = $schemaManager->listTableColumns('persons_ex09');
        $hasColumn = array_key_exists('marital_status', $columns);
        $personReflection = new \ReflectionClass(\ex09Bundle\Entity\Person::class);
        $hasProperty = $personReflection->hasProperty('maritalStatus');

        if (!$hasProperty) {
            $this->addFlash('error', "La proprietà 'maritalStatus' non esiste nell'entità Person. Aggiungila prima di aggiornare lo schema e assicurati di avere le annotazioni Doctrine.");
        } elseif ($hasColumn) {
            $this->addFlash('info', "La colonna 'marital_status' esiste già nel database.");
        } else {
            try {
                $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
                $classMetadata = $em->getClassMetadata(\ex09Bundle\Entity\Person::class);
                $schemaTool->updateSchema([$classMetadata], true);
                $this->addFlash('success', "Colonna 'marital_status' aggiunta con successo tramite Doctrine ORM!");
            } catch (\Exception $e) {
                $this->addFlash('error', "Errore nell'aggiornamento dello schema: " . $e->getMessage());
            }
        }
        return $this->redirectToRoute('ex09_list');
    }

    /**
     * @Route("/ex09/create-related-entities", name="ex09_create_related_entities")
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

        return $this->redirectToRoute('ex09_list');
    }

    /**
     * @Route("/ex09/create-relationships", name="ex09_create_relationships")
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
        return $this->redirectToRoute('ex09_list');
    }

}