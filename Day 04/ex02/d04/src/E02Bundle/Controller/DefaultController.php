<?php

namespace E02Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use E02Bundle\Form\MessageType;
use Symfony\Component\Validator\Constraints as Assert;


class DefaultController extends Controller
{
    /**
     * @Route("/e02", name="e02_index")
     */ 
    public function indexAction(Request $request)
    {
        $form = $this->createForm(MessageType::class);
        $form->handleRequest($request);

        $lastLine = null;
        $success = false;
    
        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
    
            if (empty(trim($data['message'])))
                $this->addFlash('error', 'Message field cannot be blank.');
            else
            {
                $fileName = $this->getParameter('notes_file');
                $filePath = $this->get('kernel')->getRootDir() . '/../' . $fileName;
                
                $content = trim($data['message']);
                if ($data['includeTimestamp'] === 'Yes')
                {
                    $timestamp = date('Y-m-d H:i:s');
                    $content = $timestamp . ' - ' . $content;
                }
                if (file_put_contents($filePath, $content . PHP_EOL, FILE_APPEND | LOCK_EX) !== false)
                {
                    $lastLine = $content;
                    $success = true;
                    $this->addFlash('success', 'Message saved successfully!');
                }
                else
                    $this->addFlash('error', 'Error writing to file.');
            }
        }
        if (!$success)
            $lastLine = $this->getLastLineFromFile();
        return $this->render('E02Bundle:Default:index.html.twig', [
            'form' => $form->createView(),
            'lastLine' => $lastLine,
            'success' => $success
        ]);
    }
    private function getLastLineFromFile()
    {
        $fileName = $this->getParameter('notes_file');
        $filePath = $this->get('kernel')->getRootDir() . '/../' . $fileName;
        
        if (!file_exists($filePath))
            return null;
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($lines))
            return null;
        return end($lines);
    }

}