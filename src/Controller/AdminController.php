<?php

namespace App\Controller;

use App\Entity\Seller;
use App\Entity\UserNotification;
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
    public function index(SellerRepository $repository,
    AdminNotificationRepository $adminNotificationRepository): Response
    {
        $nbrNotificationNonLue = $adminNotificationRepository->count(['isRead' => false]);

        $demandes = $repository->findBy(['status' => 'pending']);
        return $this->render('pages/admin/index.html.twig', [
            'demandes' => $demandes,
            'nbrNotificationNonLue' => $nbrNotificationNonLue
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
        $nbrNotificationNonLue = $repository->count(['isRead' => false]);

        return $this->render('pages/admin/notification_index.html.twig', [
            'notifications' => $notifications,
            'nbrNotificationNonLue' => $nbrNotificationNonLue
        ]);
    }

    /**
     * Permet d'afficher les détail d'un notification
    *
    * @param Seller $seller
    * @return Response
    */
    #[Route('/admin/detail-notification/{id}', 'admin.notification.show', methods: ['GET'])]
    public function notificationShow(Seller $seller,
    AdminNotificationRepository $repository,
    EntityManagerInterface $manager): Response
    {
        // Pour rendre une notification non lue par "lue"
        $notification = $repository->findOneBy(['seller' => $seller]);

        $notification->setIsRead(true);
        $manager->persist($notification);
        $manager->flush();

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
    EntityManagerInterface $manager,
    AdminNotificationRepository $repository): Response
    {
        // Pour empêcher que les demande déjà réfusé
        // se remettre à demande vérifié
        if($seller->getStatus() === 'refused') {
            return $this->redirectToRoute('admin.notification.index');
        }

        // Création du contenu de notification pour l'utilisateur vendeur
        $userNotification = new UserNotification();
        $userNotification->setTitle("Demande d'inscription")
        ->setContent("L'administrateur a accèpté votre demande, vous pouvez désormais publier des annonces.")
        ->setIsRead(false)
        ->setUser($seller->getUser());
        $manager->persist($userNotification);
        $manager->flush();

        // Pour rendre une notification non lue par "lue"
        $notification = $repository->findOneBy(['seller' => $seller]);
        $notification->setIsRead(true);
        $manager->persist($notification);
        $manager->flush();

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

    /**
     * This controller allow to delete a notification
    *
    * @param integer $id
    * @param EntityManagerInterface $manager
    * @param AdminNotificationRepository $repository
    * @return Response
    */
    #[Route('admin/suppression-notification/{id}', 'admin.notification.delete', methods: ['GET'])]
    public function notificationDelete(int $id,
    EntityManagerInterface $manager,
    AdminNotificationRepository $repository): Response
    {
        // Pour eviter les erreur, il faut empêcher l'utilisateur
        // de supprimer une notification qui n'existe pas.
        // Ici on n'a pas utilisé le param converter parceque
        // c'est un cas spécial.
        $adminNotification = $repository->findOneBy(['id' => $id]);
        if($adminNotification == null || $adminNotification->getSeller()->getStatus() == 'pending') {
            return $this->redirectToRoute('admin.notification.index');
        }

        $manager->remove($adminNotification);
        $manager->flush();

        return $this->redirectToRoute('admin.notification.index');
    }


}
