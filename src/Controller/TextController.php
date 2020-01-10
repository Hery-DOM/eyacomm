<?php


namespace App\Controller;


use App\Entity\Page;
use App\Form\PageType;
use App\Repository\ContextRepository;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TextController extends AbstractController
{
    /**
     * @Route("/a/text",name="text_home")
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
     * @Route("/a/text/update/{id}", name="text_update")
     */
    public function textUpdate($id, PageRepository $pageRepository, Request $request, EntityManagerInterface $entityManager)
    {
        // get page according with ID
        $page = $pageRepository->find($id);

        // form creation
        $form = $this->createForm(PageType::class, $page);
        $formView = $form->createView();

        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isValid() && $form->isSubmitted()){
                $entityManager->persist($page);
                $entityManager->flush();
                $this->addFlash('info','Données modifiées');
                return $this->redirectToRoute('text_update',[
                    'id' => $id
                ]);
            }

        }

        return $this->render("back-office/text_update.html.twig",[
            'form' => $formView
        ]);
    }

    /**
     * @Route("/a/text/create", name="text_create")
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
     * @Route("/a/text/remove/{id}",name="text_remove")
     */
    public function textRemove(EntityManagerInterface $entityManager, PageRepository $pageRepository, $id)
    {
        $page = $pageRepository->find($id);
        $entityManager->remove($page);
        $entityManager->flush();

        return $this->redirectToRoute('text_home');
    }

}