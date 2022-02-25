<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(EntityManagerInterface $em, SluggerInterface $slugger, Request $request): Response
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $formView = $form->createView();

        if ($form->isSubmitted()) {
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
     */
    public function edit(
        $id,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        Request $request,
        CategoryRepository $categoryRepository
    ) {
        $category = $categoryRepository->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
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
