<?php

namespace App\Cart;

use App\Cart\CartItem;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart(){
        return $this->session->get('cart', []);
    }

    protected function saveCart($cart){
        $this->session->set('cart', $cart);
    }

    public function add(int $id)
    {
        // 1 从 session 中获取【购物车】，购物车是一个数组： 商品ID => 数量
        // 2 如果不存在，就创建一个新的空数组
        $cart = $this->getCart();

        // 3 商品ID是否存在
        // 4 如果存在增加数量
        // 5 如果不存在，添加商品，数量为1
        if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        // 6 将更新后的数据，存到 session 中
        $this->saveCart($cart);
    }

    public function getTotal(): int
    {
        $total = 0;
        foreach ($this->getCart() as $id => $qty){
            $product = $this->productRepository->find($id);
            if (!$product) {
                continue;
            }
            $total += ($product->getPrice() * $qty);
        }
        return $total;
    }

    /**
     * @return CartItem[]
     */
    public function getDetailCart(): array
    {
        $detailCart = [];
        foreach ($this->getCart() as $id => $qty){
            $product = $this->productRepository->find($id);
            if (!$product) {
                continue;
            }
            $detailCart[] = new CartItem($product, $qty);
        }
        return $detailCart;
    }

    public function remove(int $id)
    {
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->saveCart($cart);
    }

    public function decrement(int $id)  
    {
        $cart = $this->getCart();
        if (array_key_exists($id, $cart)){
            if ($cart[$id] == 1) {
                $this->remove($id);
                return;
            }
            
            $cart[$id]--;
            $this->saveCart($cart);
        }
    }

    public function empty(){
        $this->saveCart([]);
    }
}
