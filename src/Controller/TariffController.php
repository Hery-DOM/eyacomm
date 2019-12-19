<?php


namespace App\Controller;

use App\Entity\Tariff;
use App\Form\TariffType;
use App\Repository\TariffRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class TariffController extends AbstractController
{
    /**
     * @Route("/insert_tariff", name="insert_tariff")
     */
    public function insertTarrif(EntityManagerInterface $entityManager, Request $request)
    {
        $tariff = new Tariff();

        $formTariff = $this->createForm(TariffType::class, $tariff);

        $formTariffView = $formTariff->createView();

        if ($request->isMethod('POST')){

            $formTariff->handleRequest($request);

            $entityManager->persist($tariff);
            $entityManager->flush();
        }


        return $this->render('insert_tariff.html.twig',
            [
                'tariff' =>$formTariffView,
            ]
        );
    }


    /**
     * @Route("tariff/update_tariff/{id}", name="update_tariff")
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

                return $this->redirectToRoute('update_tariff',[
                    "id" => $id
                ]);
            }


        }

        return $this->render('update_tariff.html.twig',
        [
            'tariff' => $formTariffView,
        ]

        );
    }


    /**
     * @Route("delete_tariff/{id}", name="delete_tariff")
     */
    public function deleteTariff($id, EntityManagerInterface $entityManager, Request $request, TariffRepository $tariffRepository)
    {
       $tariff = $tariffRepository->find($id);

       $entityManager->remove($tariff);
       $entityManager->flush();

       return $this->redirectToRoute('list_tariff');


    }

    /**
     * @Route("/list_tariff", name="list_tariff")
     */
    public function listTariff(TariffRepository $tariffRepository)
    {
        $tariffs = $tariffRepository->findAll();

        return $this->render('list_tariff.html.twig',
        [
           'tariffs'=>$tariffs,
        ]
        );
    }



}