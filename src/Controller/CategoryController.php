<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    // protected $categoryRepository;
    // public function __construct(CategoryRepository $categoryRepository)
    // {
    //     $this->$categoryRepository = $categoryRepository;
    // }

    // public function renderMenuList()
    // {
    //     $categories = $this->categoryRepository->findAll();
    // }

    /**
     * @Route("/admin/category/create", name="category_create")
     * @IsGranted("ROLE_ADMIN", message="Vous n'avez pas le droit d'acceder à cette ressource")
     */
    public function create(EntityManagerInterface $em, SluggerInterface $slugger, Request $request): Response
    {
//        $this->denyAccessUnlessGranted("ROLE_ADMIN", null, "Vous n'avez pas le droit d'acceder
//        à cette ressource");
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $formView = $form->createView();

        if ($form->isSubmitted() && $form->isValid()) {
            $category
                ->setSlug(strtolower($slugger->slug($category->getName())))
                ->setName(ucwords($category->getName()));

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     * @IsGranted("ROLE_ADMIN", message="Vous n'avez pas le droit d'acceder à cette ressource")
     */
    public function edit(
        $id,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        Request $request,
        CategoryRepository $categoryRepository
    ) {
//        $this->denyAccessUnlessGranted("ROLE_ADMIN", null, "Vous n'avez pas le droit d'acceder à
//        cette ressource");
//        $user = $security->getUser();
//
//        if ($user === null)
//            return $this->redirectToRoute("security_login");
//
//        if ($security->isGranted("ROLE_ADMIN") === false)
//            throw new AccessDeniedHttpException("Vous n'avez pas le droit d'acceder à cette ressource");

        $category = $categoryRepository->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category
                ->setSlug(strtolower($slugger->slug($category->getName())))
                ->setName(ucwords($category->getName()));

            $em->flush();

            return $this->redirectToRoute('homepage');
        }
        $formView = $form->createView();
        return $this->render('category/edit.html.twig', ['category' => $category, 'formView' => $formView]);
    }
}
