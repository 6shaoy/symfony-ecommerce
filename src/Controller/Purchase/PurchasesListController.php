<?php

namespace App\Controller\Purchase;

use Twig\Environment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchasesListController extends AbstractController
{
    protected $security;
    protected $x;
    protected $twig;

    public function __construct(Security $security, Environment $twig)
    {
        $this->security = $security;
        $this->twig = $twig;
    }
    /**
     * @Route("/purchases", name="purchase_index")
     */
    public function index(){
        /** @var User */
        $user = $this->security->getUser();
        if (!$user){
            throw new AccessDeniedException('Vous devez connecter');
        }
        $purchases = $user->getPurchases();
        $html = $this->twig->render('purchase/index.html.twig', [
            'purchases' => $purchases
        ]);
        return new Response($html);
    }
}