<?php

namespace App\Controller;

use App\Entity\Seller;
use App\Repository\SellerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    /**
     * Cette méthode permet d'afficher le tableau de bord vendeur
    *
    * @param SellerRepository $repository
    * @return Response
    */
    #[Route('/admin', name: 'admin.index', methods: ['GET'])]
    public function index(SellerRepository $repository): Response
    {
        $demandes = $repository->findBy(['status' => 'pending']);
        return $this->render('pages/admin/index.html.twig', [
            'demandes' => $demandes
        ]);
    }

    /**
     * Cette méthode permet d'afficher la liste des notifications
     * pour l'administrateur
    *
    * @param SellerRepository $repository
    * @return Response
    */
    #[Route('/admin/notification', 'admin.notification.index', methods: ['GET'])]
    public function notificationIndex(SellerRepository $repository): Response
    {
        $sellers = $repository->findBy(['status' => 'pending']);
        return $this->render('pages/admin/notification_index.html.twig', [
            'sellers' => $sellers
        ]);
    }

    /**
     * Permet d'afficher les détail d'un notification
    *
    * @param Seller $seller
    * @return Response
    */
    #[Route('/admin/detail-notification/{id}', 'admin.notification.show', methods: ['GET'])]
    public function notificationShow(Seller $seller): Response
    {
        return $this->render('pages/admin/notification_show.html.twig', [
            'seller' => $seller
        ]);
    }

    /**
     * Permet d'afficher le document justificatif du vendeur
    *
    * @param Seller $seller
    * @return Response
    */
    #[Route('/admin/document/{id}', 'admin.document.show', methods: ['GET'])]
    public function documentShow(Seller $seller): Response
    {
        return $this->render('pages/admin/document_show.html.twig', [
            'seller' => $seller
        ]);
    }

    /**
     * Permet de rejeter la demande d'inscription du vendeur
    *
    * @param Seller $seller
    * @param EntityManagerInterface $manager
    * @return void
    */
    #[Route('/admin/suppression-notification/{id}', 'admin.notification.delete', methods: ['GET'])]
    public function notificationDelete(Seller $seller,
    EntityManagerInterface $manager)
    {
        $seller->setStatus('refused');
        $manager->persist($seller);
        $manager->flush();
        return $this->redirectToRoute('admin.notification.index');
    }

    /**
     * Permet de confirmer la demande du vendeur
    *
    * @param Seller $seller
    * @param EntityManagerInterface $manager
    * @return Response
    */
    #[Route('/admin/confirmation-notification/{id}', 'admin.notification.confirm', methods: ['GET'])]
    public function notificationConfirm(Seller $seller,
    EntityManagerInterface $manager): Response
    {
        $seller->setStatus('verified');

        $user = $seller->getUser();
        $user->setRoles(['ROLE_SELLER']);
        $manager->persist($seller);
        $manager->flush();

        $this->addFlash(
            'success',
            'Demande accèptée avec succès !'
        );

        return $this->redirectToRoute('admin.notification.index');
    }


}
