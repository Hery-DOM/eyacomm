<?php


namespace App\Controller;


use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    /**
     * @Route("/a/category", name="show_categories")
     */
    public function showCategories(CategoryRepository $categoryRepository)
    {
        // get every categories
        $categories = $categoryRepository->findAll();

        return $this->render('back-office/categories.html.twig',[
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/a/category/new", name="create_category")
     * To create a new category
     */
    public function createCategory(Request $request, EntityManagerInterface $entityManager)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $formView = $form->createView();

        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isValid() && $form->isSubmitted()){
                $entityManager->persist($category);
                $entityManager->flush();
                return $this->redirectToRoute('show_categories');
            }

        }

        return $this->render('back-office/category_create.html.twig',[
            'form' => $formView
        ]);

    }

    /**
     * @Route("/a/category/update/{id}", name="update_category")
     * To update a category, id in wild card
     */
    public function updateCategory($id, CategoryRepository $categoryRepository, Request $request,
                                   EntityManagerInterface $entityManager)
    {
        // get the correct category with ID
        $category = $categoryRepository->find($id);

        if(empty($category)){
            return $this->redirectToRoute('show_categories');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $formView = $form->createView();

        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isValid() && $form->isSubmitted()){
                $entityManager->persist($category);
                $entityManager->flush();
                return $this->redirectToRoute('show_categories');
            }
        }

        return $this->render('back-office/category_update.html.twig',[
            'form' => $formView
        ]);

    }

}