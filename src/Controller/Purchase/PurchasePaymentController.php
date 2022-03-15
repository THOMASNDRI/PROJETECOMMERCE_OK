<?php


namespace App\Controller\Purchase;


use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentController extends AbstractController
{
    /**
     * @Route("/purchase/pay/{id}", name="purchase_payment_form")
     * @IsGranted("ROLE_USER")
     */
    public function showCardForm($id, PurchaseRepository $purchaseRepository){
        $purchase = $purchaseRepository->find($id);
        if (!$purchase || ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)){
            return $this->redirectToRoute('cart_show');
        }

        return $this->render('purchase/payment.html.twig');
    }
}