<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id": "\d+"})
     */
    public function add($id, SessionInterface $session, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('produit n\'existe pas');
        }

        // 1 从 session 中获取【购物车】，购物车是一个数组： 商品ID => 数量
        // 2 如果不存在，就创建一个新的空数组
        $cart = $session->get('cart', []);

        // 3 商品ID是否存在
        // 4 如果存在增加数量
        // 5 如果不存在，添加商品，数量为1
        if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        
        // 6 将更新后的数据，存到 session 中
        $session->set('cart', $cart);

        // $session->remove('cart');
        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }
}
