<?php

namespace ex03Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use ex03Bundle\Entity\User;
use ex03Bundle\Form\UserType;

class DefaultController extends Controller
{
    /**
     * @Route("/ex03", name="ex03_index")
     */
    public function indexAction()
    {
        return $this->render('ex03Bundle:Default:index.html.twig');
    }

    /**
     * @Route("/ex03/create-table", name="ex03_create_table")
     */
    public function createTableAction()
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
            $classes = [$em->getClassMetadata(User::class)];
            $schemaTool->updateSchema($classes, true);
            return $this->render('ex03Bundle:Default:create_table.html.twig', [
                'message' => 'Table created successfully!',
                'success' => true
            ]);
        }
        catch (\Exception $e)
        {
            return $this->render('ex03Bundle:Default:create_table.html.twig', [
                'message' => 'Error: ' . $e->getMessage(),
                'success' => false
            ]);
        }
    }

    /**
     * @Route("/ex03/form", name="ex03_form")
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
                return $this->redirectToRoute('ex03_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error saving user: ' . $e->getMessage());
            }
        }

        return $this->render('ex03Bundle:Default:form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ex03/list", name="ex03_list")
     */
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('ex03Bundle:Default:list.html.twig', [
            'users' => $users,
        ]);
    }
}