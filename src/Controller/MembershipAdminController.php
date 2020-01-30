<?php


namespace App\Controller;


use App\Entity\Invoice;
use App\Entity\User;
use App\Form\InvoiceType;
use App\Form\UserType;
use App\Repository\InvoiceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MembershipAdminController extends AbstractController
{
    /**
     * @Route("/admin/members", name="admin_members")
     * To show every customers
     */
    public function membershipHome(UserRepository $userRepository)
    {
        // get every users with role = "ROLE_USER"
        $users = $userRepository->findAll();

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
        // get user's ID
        $id = $request->query->get('id');

        // get user with his ID
        $user = $userRepository->find($id);

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
            'form' => $formView
        ]);
    }

    /**
     * @Route("/admin/members/delete", name="admin_members_delete")
     * To delete an user / no view
     */
    public function membersDelete(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        // get user's ID
        $id = $request->query->get('id');

        //get user
        $user = $userRepository->find($id);

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_members');
    }



}