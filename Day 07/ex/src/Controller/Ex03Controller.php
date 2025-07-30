<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;


class Ex03Controller extends AbstractController
{
    #[Route('/ex03', name: 'ex03_extension')]
    public function extensionAction(TranslatorInterface $translator)
    {
        $string1 = $translator->trans('hello world');
        $string2 = $translator->trans('there are 123 numbers');

        return $this->render('ex03/ex03.html.twig', [
            'string1' => $string1,
            'string2' => $string2,
        ]);
    }
}
