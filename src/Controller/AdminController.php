<?php

namespace App\Controller;

use App\Entity\Seller;
use App\Repository\AdminNotificationRepository;
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
     * This controller allow to show the liste of admin notification
    *
    * @param AdminNotificationRepository $repository
    * @return Response
    */
    #[Route('/admin/notification', 'admin.notification.index', methods: ['GET'])]
    public function notificationIndex(AdminNotificationRepository $repository): Response
    {
        $notifications = $repository->findAll();
        return $this->render('pages/admin/notification_index.html.twig', [
            'notifications' => $notifications
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
    #[Route('/admin/refus-notification/{id}', 'admin.notification.refuse', methods: ['GET'])]
    public function notificationRefuse(Seller $seller,
    EntityManagerInterface $manager)
    {
        // Pour empêcher que les demande déjà vérifié
        // se remettre à demande réfusé
        if($seller->getStatus() === 'verified') {
            return $this->redirectToRoute('admin.notification.index');
        }

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
        // Pour empêcher que les demande déjà réfusé
        // se remettre à demande vérifié
        if($seller->getStatus() === 'refused') {
            return $this->redirectToRoute('admin.notification.index');
        }

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
