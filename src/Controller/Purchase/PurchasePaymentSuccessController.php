<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     */
    public function success($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService){
        // 1 recupere la commande
        $purchase = $purchaseRepository->find($id);
        if (
            !$purchase || 
            ($purchase && $purchase->getUser() !== $this->getUser()) || 
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID )
            ) {
            $this->addFlash('warning', 'la commande n\'exist pas');
            return $this->redirectToRoute('purchase_index');
        }

        // 2 statu PAID
        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush();

        // 3 vide le panier
        $cartService->empty();

        // 4 flash, redirect
        $this->addFlash('success', 'la commande a ete payé');
        return $this->redirectToRoute("purchase_index");
    }
}