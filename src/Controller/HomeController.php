<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    /**
     * Display the home page.
     *
     * @param VehicleRepository $vehicleRepository
     * @return Response
     */
    #[Route('/', name: 'home.index', methods: ['GET'])]
    public function index(VehicleRepository $vehicleRepository): Response
    {
        // Compter le nombre d'annonces active
        $nbreAnnonces = $vehicleRepository->count(['status' => 'active']);

        return $this->render('home/index.html.twig', [
            'vehicles' => $vehicleRepository->findActiveAnnonces(),
            'nbreAnnonces' => $nbreAnnonces
        ]);
    }

    /**
     * Display info vehicle
    *
    * @param Vehicle $vehicle
    * @return Response
    */
    #[Route('/detail-annonce-{id}', 'home.annonce.show', methods: ['GET'])]
    public function annonceShow(Vehicle $vehicle): Response
    {
        return $this->render('home/annonce_show.html.twig', [
            'vehicle' => $vehicle
        ]);
    }
}
