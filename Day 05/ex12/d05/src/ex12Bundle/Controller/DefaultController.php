<?php

namespace ex12Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use ex12Bundle\Entity\Person;
use ex12Bundle\Form\PersonType;

class DefaultController extends Controller
{
    /**
     * @Route("/ex12/reset-db", name="ex12_reset_db")
     */
    public function resetDbAction()
    {
        $em = $this->getDoctrine()->getManager();
        try
        {
            $em->createQuery('DELETE FROM ex12Bundle:BankAccount')->execute();
            $em->createQuery('DELETE FROM ex12Bundle:Address')->execute();
            $em->createQuery('DELETE FROM ex12Bundle:Person')->execute();
            $message = 'Database azzerato con successo!';
            $success = true;
        }
        catch (\Exception $e)
        {
            $message = 'Errore durante l\'azzeramento: ' . $e->getMessage();
            $success = false;
        }
        return $this->render('ex12Bundle:Default:create_table.html.twig', [
            'message' => $message,
            'success' => $success
        ]);
    }


    /**
     * @Route("/ex12", name="ex12_index")
     */
    public function indexAction()
    {
        return $this->render('ex12Bundle:Default:index.html.twig');
    }


    /**
     * @Route("/ex12/form", name="ex12_form")
     */
    public function formAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
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
                    $person = new Person();
                    $person->setUsername($username);
                    $person->setName($name);
                    $person->setEmail($email);
                    $person->setBirthdate(new \DateTime($birthdate));
                    $person->setEnable($enable ? true : false);
                    $em->persist($person);
                    $em->flush();
                    $this->addFlash('success', 'Person saved successfully!');
                    return $this->redirectToRoute('ex12_list');
                }
                catch (\Exception $e)
                {
                    $error = 'Error saving person: ' . $e->getMessage();
                }
            }
        }
        return $this->render('ex12Bundle:Default:form.html.twig', [
            'error' => $error
        ]);
    }

    /**
     * @Route("/ex12/list", name="ex12_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request_stack')->getCurrentRequest();
        $filterDate = $request->query->get('birthdate', '');
        $sortField = $request->query->get('sort', 'name');
        $sortDir = strtolower($request->query->get('dir', 'asc')) === 'desc' ? 'DESC' : 'ASC';
        $allowedSort = ['name', 'email', 'balance'];

        if (!in_array($sortField, $allowedSort))
            $sortField = 'name';
        if ($filterDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate))
            $filterDate = '';
        $qb = $em->createQueryBuilder();

        $qb->select('p, b')->from('ex12Bundle:Person', 'p')->leftJoin('p.bankAccount', 'b');
        if ($filterDate)
            $qb->andWhere('p.birthdate >= :filterDate')->setParameter('filterDate', $filterDate);
        if ($sortField === 'balance')
            $qb->orderBy('b.balance', $sortDir);
        else
            $qb->orderBy('p.' . $sortField, $sortDir);
        $results = $qb->getQuery()->getResult();

        return $this->render('ex12Bundle:Default:list.html.twig', [
            'results' => $results,
            'filterDate' => $filterDate,
            'sortField' => $sortField,
            'sortDir' => $sortDir
        ]);
    }

    /**
     * @Route("/ex12/delete/{id}", name="ex12_delete")
     */
    public function deleteAction($id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid person ID.');
            return $this->redirectToRoute('ex12_list');
        }
        $em = $this->getDoctrine()->getManager();
        try
        {
            $person = $em->getRepository('ex12Bundle:Person')->find($id);
            if (!$person)
            {
                $this->addFlash('error', 'Person not found.');
                return $this->redirectToRoute('ex12_list');
            }
            $em->remove($person);
            $em->flush();
            $this->addFlash('success', 'Person deleted successfully!');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error deleting person: ' . $e->getMessage());
        }
        return $this->redirectToRoute('ex12_list');
    }

    /**
     * @Route("/ex12/update/{id}", name="ex12_update")
     */
    public function updateAction(Request $request, $id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid person ID.');
            return $this->redirectToRoute('ex12_list');
        }
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository(Person::class)->find($id);

        if (!$person)
        {
            $this->addFlash('error', 'Person not found.');
            return $this->redirectToRoute('ex12_list');
        }
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            try
            {
                $em->flush();
                $this->addFlash('success', 'Person updated successfully!');
                return $this->redirectToRoute('ex12_list');
            }
            catch (\Exception $e)
            {
                $this->addFlash('error', 'Error updating person: ' . $e->getMessage());
            }
        }
        return $this->render('ex12Bundle:Default:update.html.twig', [
            'form' => $form->createView(),
            'person' => $person
        ]);
    }




}