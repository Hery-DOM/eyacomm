<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MembershipAdminController extends AbstractController
{
    /**
     * @Route("/a/members", name="admin_members")
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
     * @Route("/a/members/update", name="admin_members_update")
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
     * @Route("/a/members/delete", name="admin_members_delete")
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