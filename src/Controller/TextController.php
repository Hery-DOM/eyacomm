<?php


namespace App\Controller;


use App\Entity\Page;
use App\Form\PageType;
use App\Repository\ContextRepository;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TextController extends AbstractController
{
    /**
     * @Route("/admin/text",name="text_home")
     */
    public function textHome(ContextRepository $contextRepository, Request $request, PersonnalFunction
    $personnalFunction, PageRepository $pageRepository)
    {
        //get every context
        $contexts = $contextRepository->findAll();

        if(isset($_GET['submit1'])){
            $id = $request->query->get('id');
            $id = $personnalFunction->checkInput($id);
            $context_target = $contextRepository->find($id);
            $pages = $pageRepository->findBy(['context' => $context_target]);
            return $this->render("back-office/text.html.twig",[
                'contexts' => $contexts,
                'contextTarget' => $context_target,
                'id' => $id,
                'pages' => $pages
            ]);
        }


        return $this->render("back-office/text.html.twig",[
            'contexts' => $contexts
        ]);
    }

    /**
     * @Route("/admin/text/update/{id}", name="text_update")
     */
    public function textUpdate($id, PageRepository $pageRepository, Request $request, EntityManagerInterface $entityManager)
    {
        // get page according with ID
        $page = $pageRepository->find($id);

        // get page's context
        $context = $page->getContext();

        // form creation
        $form = $this->createForm(PageType::class, $page);
        $formView = $form->createView();

        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isValid() && $form->isSubmitted()){
                //pour ajouter une image
                /** @var UploadedFile $image */
                $image = $form['picture']->getData();

                // Condition nécessaire car le champ 'image' n'est pas requis
                // donc le fichier doit être traité que s'il est téléchargé
                if ($image) {
                    if($page->getPicture() == "outils0.png" || $page->getPicture() == "propositions0.png"){
                        $newFilename = $page->getPicture();
                    }else {
                        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                        // Nécessaire pour inclure le nom du fichier en tant qu'URL + sécurité + nom unique
                        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                        $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
                    }

                    // Déplace le fichier dans le dossier des images d'articles
                    try {
                        if(!is_null($page->getPicture())){
                            //si le champ "picture" de la table "article" n'est pas nul, on supprime le fichier
                            // correspondant
                            unlink("assets/img//".$page->getPicture());
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
                        return $this->redirectToRoute('text_update',[
                            'id' => $id
                        ]);
                    }

                    $page->setPicture($newFilename);

                }





                $entityManager->persist($page);
                $entityManager->flush();
                $this->addFlash('info','Données modifiées');
                return $this->redirectToRoute('text_update',[
                    'id' => $id
                ]);
            }else{
                $this->addFlash('info', 'Erreur lors de la soumission du formulaire');
                return $this->redirectToRoute('text_update',[
                    'id' => $id
                ]);
            }

        }

        return $this->render("back-office/text_update.html.twig",[
            'form' => $formView,
            'context' => $context,
            'page' => $page
        ]);
    }

    /**
     * @Route("/admin/text/create", name="text_create")
     */
    public function textCreate(Request $request, EntityManagerInterface $entityManager, ContextRepository $contextRepository)
    {
        // create a new page
        $page = new Page();

        //get context's ID
        $idContext = $request->query->get('idContext');
        //get context according with ID
        $context = $contextRepository->find($idContext);

        $form = $this->createForm(PageType::class, $page);
        $formView = $form->createView();

        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isValid() && $form->isSubmitted()){

                //pour ajouter une image
                /** @var UploadedFile $image */
                $image = $form['picture']->getData();

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
                        return $this->redirectToRoute('text_create');
                    }

                    $page->setPicture($newFilename);

                }

                $entityManager->persist($page);
                $page->setContext($context);
                $entityManager->flush();
                return $this->redirectToRoute('text_home');
            }
        }

        return $this->render("back-office/text_create.html.twig",[
            'form' => $formView
        ]);
    }

    /**
     * @Route("/admin/text/remove/{id}",name="text_remove")
     */
    public function textRemove(EntityManagerInterface $entityManager, PageRepository $pageRepository, $id)
    {
        $page = $pageRepository->find($id);
        unlink("assets/img//".$page->getPicture());
        $entityManager->remove($page);
        $entityManager->flush();

        return $this->redirectToRoute('text_home');
    }

}