<?php


namespace App\Controller\Purchase;


use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;


class PurchasesListController extends AbstractController
{
//    protected $security;
//    protected $router;
//
//
//    public function __construct(Security $security, RouterInterface $router)
//    {
//        $this->security = $security;
//        $this->router = $router;
//    }

    /**
     * @Route("/purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour acceder à vos commandes")
     */
    public function index(){
        // 1. Nous devons nous assurer que la personne est connecté (sinon redirection vers la page d'accueil)->securty

        /** @var User */
        $user = $this->getUser();
        //$user = $this->security->getUser();

//        if (!$user){
//            // Générer un url en fonction du nom d'une route avec UrlGeneretor out RouterInterface
//            // redirection ->RedirectResponse
////            $url = $this->router->generate('homepage');
////            return new RedirectResponse($url);
//            throw new AccessDeniedException("Vous deviez vous connecter pour acceder à vos commandes");
//        }

        // 2. Nous voulons savoir qui est connecté. ->security
        // 3. Nous voulons passer l'utilisateur connecté à twig afin d'afficher
        return $this->render('purchase/index.html.twig', ['purchases' => $user->getPurchases()]);
    }

}