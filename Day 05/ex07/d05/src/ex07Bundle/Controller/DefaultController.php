<?php

namespace ex07Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use ex07Bundle\Entity\User;
use ex07Bundle\Form\UserType;

class DefaultController extends Controller
{
    /**
     * @Route("/ex07", name="ex07_index")
     */
    public function indexAction()
    {
        return $this->render('ex07Bundle:Default:index.html.twig');
    }

    /**
     * @Route("/ex07/create-table", name="ex07_create_table")
     */
    public function createTableAction()
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
            $classes = [$em->getClassMetadata(User::class)];
            $schemaTool->updateSchema($classes, true);
            return $this->render('ex07Bundle:Default:create_table.html.twig', [
                'message' => 'Table created successfully!',
                'success' => true
            ]);
        }
        catch (\Exception $e)
        {
            return $this->render('ex07Bundle:Default:create_table.html.twig', [
                'message' => 'Error: ' . $e->getMessage(),
                'success' => false
            ]);
        }
    }

    /**
     * @Route("/ex07/form", name="ex07_form")
     */
    public function formAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'User saved successfully! (Created or updated if already existed)');
                return $this->redirectToRoute('ex07_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error saving user: ' . $e->getMessage());
            }
        }

        return $this->render('ex07Bundle:Default:form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ex07/list", name="ex07_list")
     */
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('ex07Bundle:Default:list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/ex07/delete/{id}", name="ex07_delete")
     */
    public function deleteAction($id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid user ID.');
            return $this->redirectToRoute('ex07_list');
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if (!$user)
        {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('ex07_list');
        }
        try
        {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'User deleted successfully!');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error deleting user: ' . $e->getMessage());
        }
        return $this->redirectToRoute('ex07_list');
    }

    /**
     * @Route("/ex07/update/{id}", name="ex07_update")
     */
    public function updateAction(Request $request, $id)
    {
        if (!is_numeric($id))
        {
            $this->addFlash('error', 'Invalid user ID.');
            return $this->redirectToRoute('ex07_list');
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        
        if (!$user)
        {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('ex07_list');
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            try
            {
                $em->flush();
                $this->addFlash('success', 'User updated successfully!');
                return $this->redirectToRoute('ex07_list');
            }
            catch (\Exception $e)
            {
                $this->addFlash('error', 'Error updating user: ' . $e->getMessage());
            }
        }
        return $this->render('ex07Bundle:Default:update.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}