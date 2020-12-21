<?php

namespace App\Controller;

use App\Taxes\Detector;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HelloController
{
    /**
     * @Route("/hello/{prenom<\w+>?world}", name="hello")
     */
    public function hello($prenom, Detector $detector, Environment $twig)
    {
        // dump($detector->detect(101));
        // dump($detector->detect(10));


        $html = $twig->render('hello.html.twig', [
            'prenom' => $prenom,
            'ages' => [
                12,
                18,
                29,
                8
            ]
        ]);

        return new Response($html);
    }
}