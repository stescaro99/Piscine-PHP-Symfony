<?php

namespace E03Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/e03", name="e03_index")
     */
    public function indexAction()
    {
        $numberOfColors = $this->getParameter('e03.number_of_colors');
        $baseColors = ['black', 'red', 'blue', 'green'];
        $colorShades = [];

        foreach ($baseColors as $color)
            $colorShades[$color] = $this->generateShades($color, $numberOfColors);
        return $this->render('E03Bundle:Default:index.html.twig', [
            'baseColors' => $baseColors,
            'colorShades' => $colorShades,
            'numberOfColors' => $numberOfColors
        ]);
    }

    private function generateShades($colorName, $numberOfColors)
    {
        $shades = [];
        for ($i = 0; $i < $numberOfColors; $i++)
        {
            $intensity = ($i / ($numberOfColors - 1)) * 255;
            switch ($colorName)
            {
                case 'black':
                    $shades[] = sprintf('#%02x%02x%02x', $intensity, $intensity, $intensity);
                    break;
                case 'red':
                    $shades[] = sprintf('#%02x0000', $intensity);
                    break;
                case 'blue':
                    $shades[] = sprintf('#0000%02x', $intensity);
                    break;
                case 'green':
                    $shades[] = sprintf('#00%02x00', $intensity);
                    break;
            }
        }
        return $shades;
    }
}
