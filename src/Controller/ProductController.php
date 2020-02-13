<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;   
use App\Entity\Product;
use App\Form\ProductType;

class ProductController extends AbstractController
{

    /**
     * @Route("/admin/product/insert_product", name="insert_product")
     */
    public function insertProduct(EntityManagerInterface $entityManager, Request $request)
    {
        $product = new Product();

        $formProduct = $this->createForm(ProductType::class, $product);

        $formProductView = $formProduct->createView();

        if ($request->isMethod('POST')){

            $formProduct->handleRequest($request);
            
            if($formProduct->isValid() && $formProduct->isSubmitted()){

                //pour ajouter une image
                /** @var UploadedFile $image */
                $image = $formProduct['picture']->getData();

                // Condition nécessaire car le champ 'image' n'est pas requis
                // donc le fichier doit être traité que s'il est téléchargé
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    // Nécessaire pour inclure le nom du fichier en tant qu'URL + sécurité + nom unique
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();


                    // Déplace le fichier dans le dossier des images d'articles
                    try {
                        $move = $image->move(
                            $this->getParameter('images'),
                            $newFilename
                        );
                        if (!$move) {
                            throw new FileException('Erreur lors du chargement de l\'image ');
                        }
                    } catch (FileException $e) {
                        // ... capture de l'exception
                        $this->addFlash('info', $e->getMessage());
                        return $this->redirectToRoute('insert_product');
                    }

                    $product->setPicture($newFilename);

                }

                $entityManager->persist($product);
                $entityManager->flush();

            return $this->redirectToRoute('list_product');

            }else{
                $this->addFlash('info', 'Erreur lors du chargement (ex : image > 3Mo)');
                return $this->render('back-office/insert_product.html.twig',[
                    'product' => $formProductView
                ]);
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
     * @Route("/admin/product/update_product/{id}", name="update_product")
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
                //pour ajouter une image
                /** @var UploadedFile $image */
                $image = $formProduct['picture']->getData();

                // Condition nécessaire car le champ 'image' n'est pas requis
                // donc le fichier doit être traité que s'il est téléchargé
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    // Nécessaire pour inclure le nom du fichier en tant qu'URL + sécurité + nom unique
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();


                    // Déplace le fichier dans le dossier des images d'articles
                    try {
                        if(!is_null($product->getPicture())){
                            //si le champ "picture" de la table "article" n'est pas nul, on supprime le fichier
                            // correspondant
                            unlink("assets/img//".$product->getPicture());
                        }
                        $move = $image->move(
                            $this->getParameter('images'),
                            $newFilename
                        );
                        if (!$move) {
                            throw new FileException('Erreur lors du chargement de l\'image ');
                        }
                    } catch (FileException $e) {
                        // ... capture de l'exception
                        $this->addFlash('info', $e->getMessage());
                        return $this->redirectToRoute('insert_product');
                    }

                    $product->setPicture($newFilename);

                }


                $entityManager->persist($product);
                $entityManager->flush();
                $this->addFlash('info', 'Données modifiées');
            }
        }
         
        return $this->render('back-office/update_product.html.twig',
        [
            'form'=>$formProductView,
            'product' => $product
        ]
        );
    }


    /**
     * @Route("/admin/product/delete_product/{id}", name="delete_product")
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
     * @Route("/admin/list_product", name="list_product")
     */
    public function listProduct(ProductRepository $productRepository, Request $request, PersonnalFunction $personnalFunction)
    {   
        $products = $productRepository->findAll();

        //get search
        $param = $request->query->get('search');
        $param = $personnalFunction->checkInput($param);
        if($param){
            $products = $productRepository->findBySearch($param);
        }

        return $this->render('back-office/list_product.html.twig',
        [
            'products'=>$products,
        ]
    );
    }
}