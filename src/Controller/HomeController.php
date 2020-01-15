<?php


namespace App\Controller;


use App\Repository\CategoryRepository;
use App\Repository\ContextRepository;
use App\Repository\PageRepository;
use App\Repository\ProductRepository;
use App\Repository\TariffRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{

    private function checkInput($value){
        $value = trim($value);
        $value = stripslashes($value);
        $value = htmlspecialchars($value);
        return $value;
    }

    /**
     * @Route("/offres", name="offre")
     * To show the differents offers (= tariff)
     */
    public function showOffers(TariffRepository $tariffRepository)
    {
        //get the offers
        $offers = $tariffRepository->findAll();

        return $this->render('front-office/offers.html.twig',[
            'offers' => $offers
        ]);
    }

    /**
     * @Route("/services", name="services")
     * To show the differents services
     */
    public function showServices(PageRepository $pageRepository){


        //get page for services (id = 2)
        $service_pages = $pageRepository->findByService(2);
        $service_pages = $service_pages[0];

        //get page for "outils" (id = 3)
        $tool = $pageRepository->findByService(3);
        $intro_tool = $tool[0];

        //get page for "proposition" (id = 4)
        $proposal = $pageRepository->findByService(4);
        $intro_propo = $proposal[0];

        //get page for "gammes" (id = 5)
        $range = $pageRepository->findByService(5);
        $intro_range = $range[0];

        return $this->render('front-office/services.html.twig',[
            'service' => $service_pages,
            'outils' => $intro_tool,
            'gamme' => $intro_range,
            'proposition' => $intro_propo
        ]);
    }

    /**
     * @Route("/services/category/{cat}", name="services_categories")
     * Dynamic page to see the service's category in accord to wild card
     */
    public function showTools(PageRepository $pageRepository, $cat, ContextRepository $contextRepository)
    {
        // secure the wild card
        $cat = $this->checkInput($cat);

        //get contexts
        $context_id = $contextRepository->findBy(['name' => $cat]);
        $context_id = $context_id[0]->getId();

        //get the page
        $pages = $pageRepository->findByService($context_id);

        return $this->render('front-office/services_category.html.twig',[
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/services/gammes", name="services_range")
     * To show every produtcs
     */
    public function showRange(ProductRepository $productRepository, Request $request, CategoryRepository $categoryRepository)
    {

        //get every categories
        $categories = $categoryRepository->findAll();

        //get the every products
        $products = $productRepository->findAll();

        //if there is a category in parameter
        $parameter = $request->query->get('category');
        $parameter = $this->checkInput($parameter);
        if(!empty($parameter)){
            $current_category = $parameter;
        }else{
            $current_category = "Catégories";
        }

        if(isset($parameter) && !empty($parameter) ){

            //get category's ID
            $category_id = $categoryRepository->findBy(['name' => $parameter]);

            if(!empty($category_id)){
                $category_id = $category_id[0]->getId();
                $products = $productRepository->findBy(['category' => $category_id]);
            }

        }

        // get thread
        $thread = "Nos services / Nos gammes";

        return $this->render('front-office/every_products.html.twig',[
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $current_category,
            'parameter' => $parameter,
            'thread' => $thread
        ]);
    }

    /**
     * @Route("/services/gammes/{product}", name="services_product")
     * To show single product
     */
    public function showProduct($product, ProductRepository $productRepository)
    {
        //secure the wild card
        $product = $this->checkInput($product);

        // if the product exists => get it
        $product = $productRepository->findOneBy(['name' => $product]);
        if(!empty($product)){
            return $this->render('front-office/product.html.twig',[
                'product' => $product
            ]);
        }else{
            return $this->redirectToRoute('services_range');
        }
    }

    /**
     * @Route("/contact", name="contact")
     * Page's contact
     */
    public function contact()
    {
        if(isset($_POST['submit'])){

            // secure input
            $society = $_POST['name'];
            $activity = $_POST['activity'];
            $address = $_POST['address'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $message_input = $_POST['message'];


            $to = "mail@mail.fr";
            $subject = "Message via le site d'Eyacomm";
            $message = "\n\rVous avez reçu un mail de la société ".$society;
            $message .= "\n\rActivité : ".$activity;
            $message .= "\n\rAdresse : ".$address;
            $message .= "\n\rNuméro de téléphone : ".$phone;
            $message .= "\n\rCourriel : ".$email;
            $message .= "\n\rSa demande : ".$message_input;

            $send = mail($to, $subject, $message);

            if($send){
                $this->addFlash('info','Votre message a bien été envoyé');
            }else{
                $this->addFlash('info', 'Une erreur est survenue lors de l\'envoi du mail, merci de recharger la page et recommencer l\'opération');
            }

        }

        return $this->render("front-office/contact.html.twig");
    }

    /**
     * @Route("/mentions-legales", name="legal")
     * To show legal notice
     */
    public function legal()
    {
        return $this->render('front-office/legal.html.twig');
    }

    /**
     * @Route("/eyaltelecomm", name="eyaltelecomm")
     * To show about eyacomm's page
     */
    public function about(PageRepository $pageRepository)
    {
        // get the page with context 'a-propos' (id = 6)
        $page = $pageRepository->findBy(['context' => 6]);
        $page = $page[0];

        // get thread
        $thread = "La société";

        return $this->render('front-office/about.html.twig',[
            'page' => $page,
            'thread' => $thread
        ]);
    }


}