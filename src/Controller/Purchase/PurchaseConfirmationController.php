<?php


namespace App\Controller\Purchase;


use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



class PurchaseConfirmationController extends AbstractController
{
   protected $cartService;
   protected $em;
    public function __construct(CartService $cartService, EntityManagerInterface $em)
    {
        $this->cartService = $cartService;
        $this->em = $em;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer une commande")
     */
    public function confirm(Request $request){

        // 1. Lire des données du formulaire
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        //2. si le formulaire n'a pas été soumis alors degage
        if (!$form->isSubmitted()){
            //message flash puir redirection
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
            return $this->redirectToRoute('cart_show');
        }
        // 3. Si je ne suis pas connecté: dégager (security)
        $user = $this->getUser();
//        if (!$user){
//            throw new AccessDeniedException('Vous devez être connecté pour confirmer une commande')
//        }
        // 4. Si il n'y a pas de produit dans mon panier: dégager (cartService)
        $cartItems = $this->cartService->getDetailedItems();
        if (count($cartItems) === 0){
            $this->addFlash('warning', 'Vous ne pouvez confirmer une commande avec un panier vide');
            return $this->redirectToRoute('cart_show');
        }

        // 5. Nous allons créer une Purchase
        /**
         * @var Purchase
         */
        $purchase = $form->getData();

        // 6. Nous allons le lier avec l'utilisateur actuellement connecté
        $purchase->setUser($user)
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
        $this->cartService->empty();
        $this->addFlash('success', 'La commande à bien été enregistré');
        return $this->redirectToRoute('purchase_index');
    }
}