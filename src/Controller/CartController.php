<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var CartService
     */
    protected $cartService;

    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }

    protected function getProduct(int $id)
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('produit n\'existe pas');
        }
        return $product;
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id": "\d+"})
     */
    public function add($id, Request $request): Response
    {
        $product = $this->getProduct($id);

        // // 1 从 session 中获取【购物车】，购物车是一个数组： 商品ID => 数量
        // // 2 如果不存在，就创建一个新的空数组
        // $cart = $session->get('cart', []);

        // // 3 商品ID是否存在
        // // 4 如果存在增加数量
        // // 5 如果不存在，添加商品，数量为1
        // if (array_key_exists($id, $cart)) {
        //     $cart[$id]++;
        // } else {
        //     $cart[$id] = 1;
        // }

        // // 6 将更新后的数据，存到 session 中
        // $session->set('cart', $cart);

        // refactoring CartService
        $this->cartService->add($id);

        // ======== Flash message =========

        // /** @var FlashBag */
        // $flashBag = $session->getBag('flashes');
        // $flashBag->add('success', 'Le produit a bien été ajouté dans le panier!');
        $this->addFlash('success', 'Le produit a bien été ajouté dans le panier!');

        // $session->remove('cart');

        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute('cart_show');
        }
        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show()
    {
        // $detailCart = [];
        // $total = 0;
        // foreach ($session->get('cart', []) as $id => $qty){
        //     $product = $productRepository->find($id);
        //     $detailCart[] = [
        //         'product' => $product,
        //         'qty' => $qty
        //     ];
        //     $total += ($product->getPrice() * $qty);
        // }

        $form = $this->createForm(CartConfirmationType::class);

        return $this->render('cart/index.html.twig', [
            'items' => $this->cartService->getDetailCart(),
            'total' => $this->cartService->getTotal(),
            'confirmationForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id": "\d+"})
     */
    public function delete($id): Response
    {
        $this->getProduct($id);

        $this->cartService->remove($id);

        $this->addFlash('success', 'le produit a ete supprime de votre panier!');

        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id": "\d+"})
     */
    public function decrement($id): Response
    {
        $this->getProduct($id);
        $this->cartService->decrement($id);
        $this->addFlash('success', 'nombre a ete decremente');
        return $this->redirectToRoute('cart_show');
    }
}
