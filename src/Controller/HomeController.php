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
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('front-office/home.html.twig');
    }

    /**
     * @Route("/offres", name="offre")
     * To show the differents offers (= tariff)
     */
    public function showOffers(TariffRepository $tariffRepository)
    {
        //get the offers
        $offers = $tariffRepository->findAll();

        //get thread
        $thread = "Nos offres";

        return $this->render('front-office/offers.html.twig',[
            'offers' => $offers,
            'thread' => $thread
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

        return $this->render('front-office/services.html.twig',[
            'service' => $service_pages,
            'outils' => $intro_tool,
            'proposition' => $intro_propo
        ]);
    }

    /**
     * @Route("/services/category/{cat}", name="services_categories")
     * Dynamic page to see the service's category in accord to wild card
     */
    public function showTools(PageRepository $pageRepository, $cat, ContextRepository $contextRepository,
                              PersonnalFunction $personnalFunction)
    {
        // secure the wild card
        $cat = $personnalFunction->checkInput($cat);

        //get thread
        $thread = "Nos services / Nos ".$cat;

        //get contexts
        $context_id = $contextRepository->findBy(['name' => $cat]);
        //if the category doesn't exist => return to services's page
        if(empty($context_id)){
            return $this->redirectToRoute('services');
        }
        $context_id = $context_id[0]->getId();

        //get the pages
        $pages = $pageRepository->findByService($context_id);
        //exclude the intro
        $pages = array_slice($pages, 1);

        return $this->render('front-office/services_category.html.twig',[
            'pages' => $pages,
            'thread' => $thread,
            'cat' => $cat
        ]);
    }

    /**
     * @Route("/equipement", name="equipement")
     * To show every products
     */
    public function showRange(ProductRepository $productRepository, Request $request, CategoryRepository
    $categoryRepository, PersonnalFunction $personnalFunction)
    {

        //get every categories
        $categories = $categoryRepository->findAll();

        //get the every products
        $products = $productRepository->findAll();

        //if there is a category in parameter
        $parameter = $request->query->get('category');
        $parameter = $personnalFunction->checkInput($parameter);
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
     * @Route("/equipement/{product}", name="equipement_product")
     * To show single product
     */
    public function showProduct($product, ProductRepository $productRepository, PersonnalFunction $personnalFunction)
    {
        //secure the wild card
        $product = $personnalFunction->checkInput($product);

        // if the product exists => get it
        $product = $productRepository->findOneBy(['name' => $product]);
        $category = $product->getCategory()->getName();

        //get thread
        $thread = "Nos services / Nos gammes / ".$category." / ".$product->getName();

        if(!empty($product)){
            return $this->render('front-office/product.html.twig',[
                'product' => $product,
                'thread' => $thread
            ]);
        }else{
            return $this->redirectToRoute('services_range');
        }
    }

    /**
     * @Route("/contact", name="contact")
     * Page's contact
     */
    public function contact(PersonnalFunction $personnalFunction)
    {
        //get thread
        $thread = "Nous contacter";

        if(isset($_POST['submit'])){

            // secure input
            $society = $personnalFunction->checkInput($_POST['name']);
            $activity = $personnalFunction->checkInput($_POST['activity']);
            $address = $personnalFunction->checkInput($_POST['address']);
            $phone = $personnalFunction->checkInput($_POST['phone']);
            $email = $personnalFunction->checkInput($_POST['email']);
            $message_input = $personnalFunction->checkInput($_POST['message']);


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

        return $this->render("front-office/contact.html.twig",[
            "thread" => $thread
        ]);
    }

    /**
     * @Route("/mentions-legales", name="legal")
     * To show legal notice
     */
    public function legal()
    {
        //get thread
        $thread = "Mentions légales";

        return $this->render('front-office/legal.html.twig',[
            'thread' => $thread
        ]);
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

    /**
     * @Route("/devis", name="quote")
     * To show quote's form
     */
    public function quote(PersonnalFunction $personnalFunction)
    {
        // get thread
        $thread = "Devis";

        if(isset($_POST['submit_quote'])){

            if(!empty($_POST['society']) && !empty($_POST['name']) && !empty($_POST['address']) && !empty
                ($_POST['phone'])){
                foreach($_POST as $key => $value){
                    $title = $personnalFunction->checkInput($key);
                    $$title = $personnalFunction->checkInput($value);
                }

                $to = "mail@mail.fr";
                $subject = "Demande de devis";
                $message = "Vous avez une demande de devis : ";
                $message .= "\n\r Nom de la société : ".$society;
                $message .= "\n\r Nom de l'interlocuteur : ".$name;
                $message .= "\n\r Adresse : ".$address;
                $message .= "\n\r Numéro de téléphone : ".$phone;
                $message .= "\n\r SITUATION ACTUELLE";
                $message .= "\n\r Opérateur actuelle : ".$operator;
                $message .= "\n\r Date de fin d'engagement : ".$ending;
                $message .= "\n\r INTERNET : ".$internet;
                $message .= "\n\r ADSL : ".$adsl;
                $message .= "\n\r SDSL : ".$sdsl;
                $message .= "\n\r Fibre :  ".$fibre;
                $message .= "\n\r Nombre de TO ou ligne numeris : ".$numeris;
                $message .= "\n\r Nombre d'appels en simultanée : ".$calls;
                $message .= "\n\r Nombre de lignes analogiques : ".$analogic;
                $message .= "\n\r Coût mensuel : ".$coast;
                $message .= "\n\r MATERIEL";
                $message .= "\n\r Nombre de postes filaires : ".$wire;
                $message .= "\n\r Nombre de postes sans fil : ".$wireless;
                $message .= "\n\r Âge du matériel : ".$age;
                $message .= "\n\r En location : ".$location;
                $message .= "\n\r Coût mensuel : ".$coast_material;
                $message .= "\n\r Commentaires : ".$comments;

                $send = mail($to, $subject, $message);

                if($send){
                    $this->addFlash('info','Votre message a bien été envoyé');
                }else{
                    $this->addFlash('info', 'Une erreur est survenue lors de l\'envoi du mail, merci de recharger la page et recommencer l\'opération');
                }
            }else{
                $this->addFlash('info', 'Merci de renseigner tous les champs obligatoires');
            }
        }

        return $this->render('front-office/quote.html.twig',[
            'thread' => $thread
        ]);
    }

    /**
     * @Route("/espace-client", name="membership")
     * To see customer's bills
     */
    public function membership()
    {
        // get thread
        $thread = "Espace client";

        return $this->render('front-office/membership.html.twig',
            [
                'thread' => $thread
            ]);
    }



    /**
     * @Route("/eyal-telecom/{context}", name="page_context")
     */
    public function mobile($context, PageRepository $pageRepository, PersonnalFunction $personnalFunction)
    {

        $context = $personnalFunction->checkInput($context);

        $contexts_bdd = [
            'offres-mobiles' => 7,
            'partie-fixe' => 8,
            'internet' => 9,
            'partenaires' => 10
        ];

        //check if context exists
        $check_array = array_key_exists($context, $contexts_bdd);
        if(!$check_array){
            return $this->redirectToRoute('home');
        }

        //get thread
        switch ($context){
            case 'offres-mobiles':
                $thread = "Nos offres mobiles";
                break;
            case 'partie-fixe':
                $thread = "La partie fixe";
                break;
            case 'internet':
                $thread = "Internet";
                break;
            case 'partenaires':
                $thread = "Nos partenaires";
                break;
        }

        // get the context's ID
        $id = $contexts_bdd[$context];

        //get page with context = "offres mobiles", id=7
        $page = $pageRepository->findBy(['context' => $id])[0];

        return $this->render('front-office/page.html.twig',[
            'page' => $page,
            'thread' => $thread
        ]);
    }


}