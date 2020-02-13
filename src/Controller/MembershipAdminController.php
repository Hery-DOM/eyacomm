<?php


namespace App\Controller;


use App\Entity\Invoice;
use App\Entity\User;
use App\Form\InvoiceType;
use App\Form\UserType;
use App\Repository\InvoiceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MembershipAdminController extends AbstractController
{
    /**
     * @Route("/admin/home", name="admin_members")
     * To show every customers
     */
    public function membershipHome(UserRepository $userRepository, Request $request, PersonnalFunction $personnalFunction)
    {
        // get every users with role = "ROLE_USER"
        $users = $userRepository->findAll();

        $param = $request->query->get('search');
        $param = $personnalFunction->checkInput($param);
        if($param){
            $users   = $userRepository->findBySearch($param);
        }

        return $this->render('back-office/members.html.twig',[
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/members/invoices", name="admin_members_invoices")
     * To see every invoices according with user
     */
    public function memberInvoices(Request $request, UserRepository $userRepository, InvoiceRepository $invoiceRepository)
    {
        // get user's id
        $id = $request->query->get('id');

        // get user
        $user = $userRepository->find($id);

        // get invoices
        $invoices = $invoiceRepository->findBy(['user' => $id]);

        return $this->render('back-office/members_invoices.html.twig',[
            'invoices' => $invoices,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/members/invoices/add/{id}", name="admin_members_invoice_add")
     * {id} is user's ID
     */
    public function memberInvoiceAdd($id, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        // get user
        $user = $userRepository->find($id);

        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $formView = $form->createView();

        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isValid() && $form->isSubmitted()){
                //pour ajouter une image
                /** @var UploadedFile $document */
                $document = $form['name']->getData();

                // Condition nécessaire car le champ 'image' n'est pas requis
                // donc le fichier doit être traité que s'il est téléchargé
                if ($document) {
                    $originalFilename = pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME);
                    // Nécessaire pour inclure le nom du fichier en tant qu'URL + sécurité + nom unique
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $document->guessExtension();


                    // Déplace le fichier dans le dossier des images d'articles
                    try {
                        $move = $document->move(
                            $this->getParameter('invoices'),
                            $newFilename
                        );
                        if (!$move) {
                            throw new FileException('Erreur lors du chargement du document');
                        }
                    } catch (FileException $e) {
                        // ... capture de l'exception
                        $this->addFlash('info', $e->getMessage());
                        return $this->redirectToRoute('admin_members_invoices_add');
                    }

                    $invoice->setName($newFilename);
                    $invoice->setUser($user);
                    $entityManager->persist($invoice);
                    $entityManager->flush();
                    return $this->redirectToRoute('admin_members_invoices',[
                        'id' => $id
                    ]);



                }
            }else{
                $this->addFlash('info','Erreur lors de la validation (ex : format PDF)');
            }
        }


        return $this->render('back-office/members_invoice_add.html.twig',[
            'form' => $formView
        ]);
    }

    /**
     * @Route("/admin/members/invoice/delete/{id}", name="admin_members_invoice_delete")
     * {id} is invoice's ID
     */
    public function membersInvoiceDelete($id, InvoiceRepository $invoiceRepository, EntityManagerInterface $entityManager)
    {
        // get invoice
        $invoice = $invoiceRepository->find($id);

        // get user's ID
        $id = $invoice->getUser()->getId();

        // to delete the file
        unlink("assets/pdf//".$invoice->getName());

        $entityManager->remove($invoice);
        $entityManager->flush();



        return $this->redirectToRoute('admin_members_invoices',[
            'id' => $id
        ]);
    }

    /**
     * @Route("/admin/members/update", name="admin_members_update")
     * To update an user
     */
    public function membersUpdate(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $compta = '';
        // get user's ID
        $id = $request->query->get('id');

        // get user with his ID
        $user = $userRepository->find($id);
        $roles = $user->getRoles();
        $admin = 0;

        foreach($roles as $role){
            if($role == 'ROLE_ADMIN'){
                $compta = 'limité';
                $admin = 1;
            }

            if($role == 'ROLE_SUPER_ADMIN'){
                $admin = 1;
            }
        }


        //create form
        $form = $this->createForm(UserType::class, $user);
        $formView = $form->createView();

        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isValid() && $form->isSubmitted()){
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('info','Mise à jour effectuée');
                return $this->redirectToRoute('admin_members_update',[
                    'id' => $id
                ]);
            }
        }

        return $this->render('back-office/members_update.html.twig',[
            'form' => $formView,
            'user' => $user,
            'compta' => $compta,
            'admin' => $admin
        ]);
    }

    /**
     * @Route("/admin/members/delete", name="admin_members_delete")
     * To delete an user / no view
     */
    public function membersDelete(Request $request, UserRepository $userRepository, EntityManagerInterface
    $entityManager, InvoiceRepository $invoiceRepository)
    {
        // get user's ID
        $id = $request->query->get('id');

        //get user
        $user = $userRepository->find($id);

        // get invoices
        $invoices = $invoiceRepository->findBy(['user' => $id]);
        //delete files
        if(!empty($invoices)){
            foreach($invoices as $invoice){
                unlink("assets/pdf//".$invoice->getName());
                $entityManager->remove($invoice);
            }
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_members');
    }

    /**
     * @Route("/admin/members/email", name="admin_members_email")
     */
    public function memberEmail(Request $request, UserRepository $userRepository, PersonnalFunction $personnalFunction)
    {
        // get user's ID
        $id = $request->query->get('id');

        //get user
        $user = $userRepository->find($id);

        if(isset($_POST['submit'])){
            $to = $user->getEmail();
            $subject = $personnalFunction->checkInput($_POST['subject']);
            $message = $personnalFunction->checkInput($_POST['message']);
            $headers = 'From: n.eyaletelcom@yahoo.fr' . "\r\n";

            mail($to, $subject, $message, $headers);
            $this->addFlash('info', 'Message envoyé');



        }

        return $this->render('back-office/members_email.html.twig',[
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/members/general/email", name="admin_members_general_email")
     */
    public function membersEmail(UserRepository $userRepository, PersonnalFunction $personnalFunction)
    {
        // get every users
        $users = $userRepository->findAll();

        if(isset($_POST['submit'])){
            $to = [];
            foreach($_POST as $key => $value){
                if(preg_match("#recipient#", $key)){
                    $to[] = $personnalFunction->checkInput($value);
                }

            }
            $subject = $personnalFunction->checkInput($_POST['subject']);
            $message = $personnalFunction->checkInput($_POST['message']);
            $headers = 'From: n.eyaletelcom@yahoo.fr' . "\r\n";

            foreach($to as $to){
                mail($to, $subject, $message, $headers);
            }

            $this->addFlash('info','Messages envoyés');


        }


        return $this->render('back-office/members_general_email.html.twig',[
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/member/new", name="admin_member_new")
     */
    public function memberNew(UserManagerInterface $manager, EntityManagerInterface $entityManager, PersonnalFunction
    $personnalFunction)
    {

        if(isset($_POST['submit'])){
            $username = $personnalFunction->checkInput($_POST['username']);
            $email = $personnalFunction->checkInput($_POST['email']);
            $psw = $personnalFunction->checkInput($_POST['password']);
            $confirm = $personnalFunction->checkInput($_POST['confirm']);

            if($psw != $confirm){
                $this->addFlash('info','Les mots de passes ne sont pas identiques');
                return $this->render("back-office/member_new.html.twig");
            }

            $user_check = $manager->findUserByEmail($email);
            if($user_check){
                $this->addFlash('info','L\'adresse mail est déjà utilisé');
                return $this->render("back-office/member_new.html.twig");
            }

            $user = $manager->createUser();
            $user->setUsername($username);
            $user->setPlainPassword($psw);
            $user->setEmail($email);
            $user->setEmailCanonical($email);
            $user->setEnabled(1);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('info','Client créé');
        }



        return $this->render("back-office/member_new.html.twig");
    }

    /**
     * @Route("/admin/member/update/password/{id}",name="admin_member_password")
     */
    public function memberPassword($id, UserRepository $userRepository, PersonnalFunction $personnalFunction)
    {
        // get user by ID
        $user = $userRepository->find($id);

        if(isset($_POST['submit'])){

            $psw = $personnalFunction->checkInput($_POST['psw']);
            $check = $personnalFunction->checkInput($_POST['check']);

            if($psw == $check){
                $user->setPlainPassword($psw);

                $this->addFlash('info','Mot de passe modifié');
            }else{
                $this->addFlash('info', 'Les mots de passes ne sont pas identiques');
            }
        }
        return $this->render('back-office/member_password.html.twig',[
            'user' => $user
        ]);

    }



}