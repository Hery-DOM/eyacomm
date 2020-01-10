<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;   
use App\Entity\Product;
use App\Form\ProductType;

class ProductController extends AbstractController
{

    /**
     * @Route("product/insert_product", name="insert_product")
     */
    public function insertProduct(EntityManagerInterface $entityManager, Request $request)
    {
        $product = new Product();

        $formProduct = $this->createForm(ProductType::class, $product);

        $formProductView = $formProduct->createView();

        if ($request->isMethod('POST')){

            $formProduct->handleRequest($request);
            
            if($formProduct->isValid() && $formProduct->isSubmitted()){
                $entityManager->persist($product);
                $entityManager->flush();

            return $this->redirectToRoute('list_product');

            }

        }

        return $this->render('back-office/insert_product.html.twig'
        ,
            [
                "product"=>$formProductView 
            ]
        );
    }

    /**
     * @Route("product/update_product/{id}", name="update_product")
     */
    public function updateProduct(EntityManagerInterface $entityManager, 
    ProductRepository $productRepository, Request $request, $id )
    {
        $product = $productRepository->find($id);

        $formProduct = $this->createForm(ProductType::class, $product);

        $formProductView = $formProduct->createView();

        if ($request->isMethod('POST')){

            $formProduct->handleRequest($request);
            
            if($formProduct->isValid() && $formProduct->isSubmitted()){
                $entityManager->persist($product);
                $entityManager->flush();
            
            return $this->redirectToRoute('list_product',
            [
                'id'=>$id
            ] );

            }    
        }
         
        return $this->render('back-office/update_product.html.twig',
        [
            'product'=>$formProductView,
        ]
        );
    }


    /**
     * @Route("product/delete_product/{id}", name="delete_product")
     */ 
    public function deleteProduct(EntityManagerInterface $entityManager, 
    ProductRepository $productRepository, Request $request, $id)
    {
        $product = $productRepository->find($id);

        $entityManager->remove($product);
        $entityManager->flush();

        return $this->redirectToRoute('list_product');
    }

    /**
     * @Route("/list_product", name="list_product")
     */
    public function listProduct(ProductRepository $productRepository)
    {   
        $products = $productRepository->findAll(); 

        return $this->render('back-office/list_product.html.twig',
        [
            'products'=>$products,
        ]
    );
    }
}