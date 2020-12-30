<?php

namespace App\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $user;
    protected $cartService;
    protected $em;
    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->user = $security->getUser();
        $this->cartService = $cartService;
        $this->em = $em;
    }
    public function storePurchase(Purchase $purchase)
    {
        // 6 lier avec l'utilisateur actuellement connecte (security) 
        $purchase
            ->setUser($this->user)
            ->setPurchaseAt(new DateTime())
            ->setTotal($this->cartService->getTotal())
        ;

        // 7 lier avec les produits dans le panier (cartService)
        foreach($this->cartService->getDetailCart() as $cartItem){
            $purchaseItem = new PurchaseItem;
            $purchaseItem
                ->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal())
            ;
            $this->em->persist($purchaseItem);
        }

        // 8 enregistrer la commande (entityManagerInterface)
        $this->em->persist($purchase);
        $this->em->flush();
    }
}