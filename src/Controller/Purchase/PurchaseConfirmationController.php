<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmationController extends AbstractController
{
    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="vous devez connecté!")
     */
    public function confirm(Request $request, CartService $cartService, EntityManagerInterface $em)
    {
        // 1 lire les donnees du formulaire
        // FormFactoryInterface / Request
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        // 2 si le formulaire n'a pas ete soumis: degager
        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'vous devez remplir le formulaire de confirmation');
            return $this->redirectToRoute('cart_show');
        }

        // 3 si je ne suis pas connecte: degager (security)
        $user = $this->getUser();

        // 4 si il n'y a pas de produits dans mon panier: degager (cartService)
        $cartItems = $cartService->getDetailCart();
        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'le panier est vide');
            return $this->redirectToRoute('cart_show');
        }

        // 5 creer une purchase
        /** @var Purchase */
        $purchase = $form->getData();

        // 6 lier avec l'utilisateur actuellement connecte (security) 
        $purchase
            ->setUser($user)
            ->setPurchaseAt(new DateTime())
            ->setTotal($cartService->getTotal())
        ;

        // 7 lier avec les produits dans le panier (cartService)
        foreach($cartService->getDetailCart() as $cartItem){
            $purchaseItem = new PurchaseItem;
            $purchaseItem
                ->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal())
            ;
            $em->persist($purchaseItem);
        }

        // 8 enregistrer la commande (entityManagerInterface)
        $em->persist($purchase);
        $em->flush();

        $cartService->empty();
        $this->addFlash('success', 'La commande a bien ete enregistree');
        return $this->redirectToRoute('purchase_index');
    }
}



//===================== reviser =====================


// use App\Cart\CartService;
// use App\Entity\Purchase;
// use App\Entity\PurchaseItem;
// use App\Form\CartConfirmationType;
// use DateTime;
// use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\Form\FormFactoryInterface;
// use Symfony\Component\HttpFoundation\RedirectResponse;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
// use Symfony\Component\Routing\RouterInterface;
// use Symfony\Component\Security\Core\Exception\AccessDeniedException;
// use Symfony\Component\Security\Core\Security;

// class PurchaseConfirmationController
// {
//     protected $formFactory;
//     protected $router;
//     protected $security;
//     protected $cartService;
//     protected $em;

//     public function __construct(FormFactoryInterface $formFactory, RouterInterface $router, Security $security, CartService $cartService, EntityManagerInterface $em)
//     {
//         $this->formFactory = $formFactory;
//         $this->router = $router;
//         $this->security = $security;
//         $this->cartService = $cartService;
//         $this->em = $em;
//     }


//     /**
//      * @Route("/purchase/confirm", name="purchase_confirm")
//      */
//     public function confirm(Request $request, FlashBagInterface $flashBag)
//     {
//         // 1 lire les donnees du formulaire
//         // FormFactoryInterface / Request
//         $form = $this->formFactory->create(CartConfirmationType::class);
//         $form->handleRequest($request);

//         // 2 si le formulaire n'a pas ete soumis: degager
//         if (!$form->isSubmitted()) {
//             $flashBag->add('warning', 'vous devez remplir le formulaire de confirmation');
//             return new RedirectResponse($this->router->generate('cart_show'));
//         }

//         // 3 si je ne suis pas connecte: degager (security)
//         $user = $this->security->getUser();
//         if (!$user){
//             throw new AccessDeniedException('vous devez connecte pour confirmer une commande');
//         }

//         // 4 si il n'y a pas de produits dans mon panier: degager (cartService)
//         $cartItems = $this->cartService->getDetailCart();
//         if (count($cartItems) === 0) {
//             $flashBag->add('warning', 'le panier est vide');
//             return new RedirectResponse($this->router->generate('cart_show'));
//         }

//         // 5 creer une purchase
//         /** @var Purchase */
//         $purchase = $form->getData();

//         // 6 lier avec l'utilisateur actuellement connecte (security) 
//         $purchase
//             ->setUser($user)
//             ->setPurchaseAt(new DateTime())    
//         ;

//         // 7 lier avec les produits dans le panier (cartService)
//         $total = 0;
//         foreach($this->cartService->getDetailCart() as $cartItem){
//             $purchaseItem = new PurchaseItem;
//             $purchaseItem
//                 ->setPurchase($purchase)
//                 ->setProduct($cartItem->product)
//                 ->setProductName($cartItem->product->getName())
//                 ->setProductPrice($cartItem->product->getPrice())
//                 ->setQuantity($cartItem->qty)
//                 ->setTotal($cartItem->getTotal())
//             ;
//             $total += $cartItem->getTotal();
//             $this->em->persist($purchaseItem);
//         }
//         $purchase->setTotal($total);

//         // 8 enregistrer la commande (entityManagerInterface)
//         $this->em->persist($purchase);
//         $this->em->flush();

//         $flashBag->add('success', 'La commande a bien ete enregistree');
//         return new RedirectResponse($this->router->generate('purchase_index'));
//     }
// }