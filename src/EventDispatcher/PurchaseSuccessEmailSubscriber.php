<?php

namespace App\EventDispatcher;

use App\Entity\Purchase;
use App\Entity\User;
use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Security;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;
    protected $security;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer, Security $security)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return ['purchase.success' => 'sendSuccessEmail'];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        // 1. Recuperer l'utilisateur actuellement en ligne (pour connaitre son adresse)
        /**
         * @var User
         */
        $currentuser = $this->security->getUser();

        // 2. Recuperer la commande
        $purchase = $purchaseSuccessEvent->getPurchases();

        // 3. Ecrire le mail
        $email = new TemplatedEmail();
        $email->to(new Address($currentuser->getEmail(), $currentuser->getFullName()))
            ->from("contact@mail.com")
            ->subject("Bravo, votre commande ({$purchase->getId()}) a bien été prise en compte")
            ->htmlTemplate('emails/purchase_success.html.twig')
            ->context(['purchase' => $purchase, 'user' => $currentuser]);

        // 4. Envoyer l'email
        $this->mailer->send($email);
        $this->logger->info("Email envoyé vers la commande n°" . $purchaseSuccessEvent->getPurchases()->getId());
    }
}
