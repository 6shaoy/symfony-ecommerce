<?php

namespace App\Controller\Purchase;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PurchasesListController extends AbstractController
{
    /**
     * @Route("/purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="vous devez connecté")
     */
    public function index(){
        $user = $this->getUser();
        // if (!$user){
        //     throw $this->createAccessDeniedException('Vous devez connecter');
        // }
        $purchases = $user->getPurchases();
        return $this->render('purchase/index.html.twig', [
            'purchases' => $purchases
        ]);
    }
}

// ============= reviser ===============

// use Twig\Environment;
// use Symfony\Component\Security\Core\Security;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// class PurchasesListController extends AbstractController
// {
//     protected $security;
//     protected $x;
//     protected $twig;

//     public function __construct(Security $security, Environment $twig)
//     {
//         $this->security = $security;
//         $this->twig = $twig;
//     }
//     /**
//      * @Route("/purchases", name="purchase_index")
//      */
//     public function index(){
//         /** @var User */
//         $user = $this->security->getUser();
//         if (!$user){
//             throw new AccessDeniedException('Vous devez connecter');
//         }
//         $purchases = $user->getPurchases();
//         $html = $this->twig->render('purchase/index.html.twig', [
//             'purchases' => $purchases
//         ]);
//         return new Response($html);
//     }
// }