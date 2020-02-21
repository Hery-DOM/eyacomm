<?php


namespace App\Controller;

use App\Entity\Tariff;
use App\Form\TariffType;
use App\Repository\TariffRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class TariffController extends AbstractController
{
    /**
     * @Route("/jazeiDAAI842NZidsrehz8327hzkefe4224/tariff/insert_tariff", name="insert_tariff")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function insertTarrif(EntityManagerInterface $entityManager, Request $request)
    {
        $tariff = new Tariff();

        $formTariff = $this->createForm(TariffType::class, $tariff);

        $formTariffView = $formTariff->createView();

        if ($request->isMethod('POST')){

            $formTariff->handleRequest($request);

            if($formTariff->isValid() && $formTariff->isSubmitted()){
                $entityManager->persist($tariff);
                $entityManager->flush();

            return $this->redirectToRoute('list_tariff');

            }

        }


        return $this->render('back-office/insert_tariff.html.twig',
            [
                'tariff' =>$formTariffView,
            ]
        );
    }


    /**
     * @Route("/jazeiDAAI842NZidsrehz8327hzkefe4224/tariff/update_tariff/{id}", name="update_tariff")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function updateTariff(EntityManagerInterface $entityManager, Request $request, TariffRepository $tariffRepository, $id)
    {
        $tariff =$tariffRepository->find($id);

        $formTariff = $this->createForm(TariffType::class, $tariff);
        $formTariffView = $formTariff->createView();

        if ($request->isMethod('Post')){

            $formTariff->handleRequest($request);

            if($formTariff->isValid() && $formTariff->isSubmitted()){
                $entityManager->persist($tariff);
                $entityManager->flush();

                $this->addFlash('info', 'Données modifiées');

                return $this->redirectToRoute('update_tariff',[
                    "id" => $id
                ]);
            }


        }

        return $this->render('back-office/update_tariff.html.twig',
        [
            'form' => $formTariffView,
            'tariff' => $tariff
        ]

        );
    }


    /**
     * @Route("/jazeiDAAI842NZidsrehz8327hzkefe4224/tariff/delete_tariff/{id}", name="delete_tariff")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function deleteTariff($id, EntityManagerInterface $entityManager, 
    Request $request, TariffRepository $tariffRepository)
    {
       $tariff = $tariffRepository->find($id);

       $entityManager->remove($tariff);
       $entityManager->flush();

       return $this->redirectToRoute('list_tariff');


    }

    /**
     * @Route("/jazeiDAAI842NZidsrehz8327hzkefe4224/list_tariff", name="list_tariff")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function listTariff(TariffRepository $tariffRepository)
    {
        $tariffs = $tariffRepository->findAll();

        return $this->render('back-office/list_tariff.html.twig',
        [
           'tariffs'=>$tariffs,
        ]
        );
    }



}