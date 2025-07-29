<?php
namespace ex13Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use ex13Bundle\Entity\Employee;
use ex13Bundle\Form\EmployeeType;

class EmployeeController extends Controller
{
    /**
     * @Route("/ex13/employees", name="ex13_employee_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $employees = $em->getRepository(Employee::class)->findAll();
        return $this->render('ex13Bundle:Employee:list.html.twig', [
            'employees' => $employees
        ]);
    }

    /**
     * @Route("/ex13/employee/new", name="ex13_employee_new")
     */
    public function newAction(Request $request)
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            try
            {
                $em->persist($employee);
                $em->flush();
                $this->addFlash('success', 'Employee created successfully!');
                return $this->redirectToRoute('ex13_employee_list');
            } 
            catch (\Exception $e)
            {
                $this->addFlash('error', 'Error creating employee: ' . $e->getMessage());
            }
        }
        return $this->render('ex13Bundle:Employee:new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ex13/employee/edit/{id}", name="ex13_employee_edit")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $employee = $em->getRepository(Employee::class)->find($id);
        if (!$employee)
        {
            $this->addFlash('error', 'Employee not found.');
            return $this->redirectToRoute('ex13_employee_list');
        }
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            try
            {
                $em->flush();
                $this->addFlash('success', 'Employee updated successfully!');
                return $this->redirectToRoute('ex13_employee_list');
            }
            catch (\Exception $e)
            {
                $this->addFlash('error', 'Error updating employee: ' . $e->getMessage());
            }
        }
        return $this->render('ex13Bundle:Employee:edit.html.twig', [
            'form' => $form->createView(),
            'employee' => $employee
        ]);
    }

    /**
     * @Route("/ex13/employee/delete/{id}", name="ex13_employee_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $employee = $em->getRepository(Employee::class)->find($id);
        if (!$employee)
        {
            $this->addFlash('error', 'Employee not found.');
            return $this->redirectToRoute('ex13_employee_list');
        }
        try
        {
            $subordinates = $em->getRepository(Employee::class)->findBy(['manager' => $employee]);
            foreach ($subordinates as $subordinate)
                $subordinate->setManager(null);
            $em->remove($employee);
            $em->flush();
            $this->addFlash('success', 'Employee deleted successfully!');
        }
        catch (\Exception $e)
        {
            $this->addFlash('error', 'Error deleting employee: ' . $e->getMessage());
        }
        return $this->redirectToRoute('ex13_employee_list');
    }
}
