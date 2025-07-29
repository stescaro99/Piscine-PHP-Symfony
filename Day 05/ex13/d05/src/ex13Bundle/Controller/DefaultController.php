<?php

namespace ex13Bundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use ex13Bundle\Entity\Person;
use ex13Bundle\Form\PersonType;

class DefaultController extends Controller
{
    /**
     * @Route("/ex13/reset-db", name="ex13_reset_db")
     */
    public function resetDbAction()
    {
        $em = $this->getDoctrine()->getManager();
        try
        {
            $em->createQuery('DELETE FROM ex13Bundle:Employee')->execute();
            $message = 'Database azzerato con successo!';
            $success = true;
        }
        catch (\Exception $e)
        {
            $message = 'Errore durante l\'azzeramento: ' . $e->getMessage();
            $success = false;
        }
        return $this->render('ex13Bundle:Default:create_table.html.twig', [
            'message' => $message,
            'success' => $success
        ]);
    }


    /**
     * @Route("/ex13", name="ex13_index")
     */
    public function indexAction()
    {
        return $this->render('ex13Bundle:Default:index.html.twig');
    }


    /**
     * @Route("/ex13/form", name="ex13_form")
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
                    return $this->redirectToRoute('ex13_list');
                }
                catch (\Exception $e)
                {
                    $error = 'Error saving person: ' . $e->getMessage();
                }
            }
        }
        return $this->render('ex13Bundle:Default:form.html.twig', [
            'error' => $error
        ]);
    }

    /**
     * @Route("/ex13/list", name="ex13_list")
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

        $qb->select('p, b')->from('ex13Bundle:Person', 'p')->leftJoin('p.bankAccount', 'b');
        if ($filterDate)
            $qb->andWhere('p.birthdate >= :filterDate')->setParameter('filterDate', $filterDate);
        if ($sortField === 'balance')
            $qb->orderBy('b.balance', $sortDir);
        else
            $qb->orderBy('p.' . $sortField, $sortDir);
        $results = $qb->getQuery()->getResult();

        return $this->render('ex13Bundle:Default:list.html.twig', [
            'results' => $results,
            'filterDate' => $filterDate,
            'sortField' => $sortField,
            'sortDir' => $sortDir
        ]);
    }

    /**
     * @Route("/ex13/delete/{id}", name="ex13_delete")
     */
    public function deleteAction($id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid person ID.');
            return $this->redirectToRoute('ex13_list');
        }
        $em = $this->getDoctrine()->getManager();
        try
        {
            $person = $em->getRepository('ex13Bundle:Person')->find($id);
            if (!$person)
            {
                $this->addFlash('error', 'Person not found.');
                return $this->redirectToRoute('ex13_list');
            }
            $em->remove($person);
            $em->flush();
            $this->addFlash('success', 'Person deleted successfully!');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error deleting person: ' . $e->getMessage());
        }
        return $this->redirectToRoute('ex13_list');
    }

    /**
     * @Route("/ex13/update/{id}", name="ex13_update")
     */
    public function updateAction(Request $request, $id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid person ID.');
            return $this->redirectToRoute('ex13_list');
        }
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository(Person::class)->find($id);

        if (!$person)
        {
            $this->addFlash('error', 'Person not found.');
            return $this->redirectToRoute('ex13_list');
        }
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            try
            {
                $em->flush();
                $this->addFlash('success', 'Person updated successfully!');
                return $this->redirectToRoute('ex13_list');
            }
            catch (\Exception $e)
            {
                $this->addFlash('error', 'Error updating person: ' . $e->getMessage());
            }
        }
        return $this->render('ex13Bundle:Default:update.html.twig', [
            'form' => $form->createView(),
            'person' => $person
        ]);
    }




}