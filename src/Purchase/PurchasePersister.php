<?php

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister{
    protected $security;
    protected $cartService;
    protected $em;

    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    public function storePurchase(Purchase $purchase){
        // Integrer tout ce qu'il faut et persister la purchase

        // 6. Nous allons le lier avec l'utilisateur actuellement connecté
        $purchase->setUser($this->security->getUser())
            ->setPurchasedAt(new \DateTime())
            ->setTotal($this->cartService->getTotal());

        $this->em->persist($purchase);

        // 7. Nous allons le lier avec les produits qui sont dans le panier (cartService)
        //$total =0;
        foreach ($this->cartService->getDetailedItems() as $cartItem){
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal());

            //$total += $cartItem->getTotal();
            $this->em->persist($purchaseItem);

        }
        //$purchase->setTotal($total);

        // 8. Nous allons enregistrer la commande (EntityManagerInterface)
        $this->em->flush();

    }
}
