<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService{


    /**
     * CartService constructor.
     */
    protected $session;
    protected $productRepository;
    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session =$session;
        $this->productRepository = $productRepository;
    }

    protected function getCart(){
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart){
        $this->session->set('cart', $cart);
    }

    public function add(int $id){

        // 1. Retrouver le panier dans la session (sous forme de tableau)
        // 2. S'il n'existe pas encore, alors prendre un tableau vide
        $cart = $this->getCart();
        // 3. voir si le produit {id} existe dejà dans le tableau
        // 4. si c'est le cas simplement, augmente la quantité
        // 5. Sinon initialise le tableau et ajouter le produit avec la quantité
        if (!array_key_exists($id, $cart)){
            $cart[$id] = 0;
        }

            $cart[$id]++;

        // 6. Enregistrer le tableau mis à jour dans la session
        $this->saveCart($cart);
        //dd($this->session->get('cart', $cart));
    }

    public function remove(int $id){
        $cart = $this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function decrement(int $id){
        $cart = $this->getCart();
        if (!array_key_exists($id, $cart)){
            return;
        }
        // Soit le produit est à 1, alors il faut simplement supprimer
        if ($cart[$id] === 1){
            $this->remove($id);
            return;
        }
        // Soit le produit est à plusieurs, donc il faut decrementer
        $cart[$id]--;
        $this->saveCart($cart);

    }

    public function getTotal():int {
        $total = 0;
        foreach ($this->getCart() as $id => $qty){
            $product = $this->productRepository->find($id);
            if (!$product)
                continue;
            $total += ($product->getPrice() * $qty);
        }
        return $total;
    }

    public function getDetailedItems():array {
        $detailedCart = [];

        foreach ($this->getCart() as $id => $qty){
            $product = $this->productRepository->find($id);
            if (!$product)
                continue;
            $detailedCart[] = new CartItem($product, $qty);
        }
        return $detailedCart;
    }
}