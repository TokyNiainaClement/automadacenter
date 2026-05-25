<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Form\VehicleType;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SellerController extends AbstractController
{
    #[Route('/vendeur', 'seller.index', methods: ['GET'])]
    public function sellerIndex(): Response
    {
        return $this-> render('pages/seller/index.html.twig');
    }

    #[Route('/', name: 'annonce.index', methods: ["GET"])]
    public function annonceIndex(VehicleRepository $vehicleRepository): Response
    {
        return $this->render('pages/seller/annonce_index.html.twig', [
            
        ]);
    }

    #[Route('/vendeur/annonce/creation', 'annonce.new', methods: ['GET', 'POST'])]
    public function new(Request $request,
    EntityManagerInterface $manager): Response
    {
        $vehicle = new Vehicle();
        $form = $this->createForm(VehicleType::class, $vehicle);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $vehicle = $form->getData();

            $manager->persist($vehicle);
            $manager->flush();

            return $this->redirectToRoute('seller.index');
        }

        return $this->render('pages/seller/annonce_new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
