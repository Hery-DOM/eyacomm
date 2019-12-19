<?php


namespace App\Controller;


use App\Repository\TariffRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{

    /**
     * @Route("/offres", name="offre")
     * To show the differents offers
     */
    public function showOffers(TariffRepository $tariffRepository)
    {
        //get the offers
        $offers = $tariffRepository->findAll();

        return $this->render('front-office/offers.html.twig',[
            'offers' => $offers
        ]);
    }

}