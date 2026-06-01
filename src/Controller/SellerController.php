<?php

namespace App\Controller;

use App\Entity\Seller;
use App\Entity\Vehicle;
use App\Form\SellerType;
use App\Form\VehicleType;
use App\Repository\SellerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SellerController extends AbstractController
{
    /**
     * This controller display the seller dashboard
    *
    * @param SellerRepository $repository
    * @return Response
    */
    #[Route('/vendeur', 'seller.index', methods: ['GET'])]
    public function index(SellerRepository $repository): Response
    {
        $seller = $repository->findOneBy(['user' => $this->getUser()]);

        if($seller == null) {
            return $this->redirectToRoute('home.index');
        }

        return $this->render('pages/seller/index.html.twig', [
            'seller' => $seller
        ]);
    }

    /**
     * This controller allow to create a new seller
    *
    * @param Request $request
    * @param EntityManagerInterface $manager
    * @return Response
    */
    #[IsGranted('ROLE_USER')]
    #[Route('/vendeur/inscription', 'seller.new', methods: ['GET', 'POST'])]
    public function new(Request $request,
    EntityManagerInterface $manager,
    SellerRepository $repository): Response
    {
        $seller = $repository->findOneBy(['user' => $this->getUser()]);

        if($seller != null) {
            return $this->redirectToRoute('seller.index');
        }

        $seller = new Seller();
        $form = $this->createForm(SellerType::class, $seller);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $seller = $form->getData();

            $seller->setStatus('pending')
            ->setUser($this->getUser());

            $manager->persist($seller);
            $manager->flush();

            return $this->redirectToRoute('seller.index');
        }

        return $this->render('pages/seller/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller allow the seller to create a new annonce
    *
    * @param Request $request
    * @param EntityManagerInterface $manager
    * @return Response
    */
    // #[IsGranted('ROLE_SELLER')]
    #[Route('/vendeur/annonce/creation', 'annonce.new', methods: ['GET', 'POST'])]
    public function annonceNew(Request $request,
    EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('home.index');
        }

        if($this->getUser()->getRoles()[0] != 'ROLE_SELLER') {
            return $this->redirectToRoute('seller.index');
        }

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
