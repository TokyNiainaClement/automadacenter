<?php

namespace App\Controller;

use App\Entity\AdminNotification;
use App\Entity\Seller;
use App\Entity\UserNotification;
use App\Entity\Vehicle;
use App\Form\SellerType;
use App\Form\VehicleType;
use App\Repository\SellerRepository;
use App\Repository\UserNotificationRepository;
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
    public function index(SellerRepository $repository,
    UserNotificationRepository $userNotificationRepository): Response
    {
        $seller = $repository->findOneBy(['user' => $this->getUser()]);

        if ($seller == null) {
            return $this->redirectToRoute('home.index');
        }

        // Récuperer le nombre de notification non lue de l'utilisateur actuel.
        $userNotificationsNonLue = $userNotificationRepository->count([
            'user' => $this->getUser(),
            'isRead' => false
        ]);

        return $this->render('pages/seller/index.html.twig', [
            'seller' => $seller,
            'userNotificationsNonLue' => $userNotificationsNonLue
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
    public function new(
        Request $request,
        EntityManagerInterface $manager,
        SellerRepository $repository
    ): Response {
        $seller = $repository->findOneBy(['user' => $this->getUser()]);

        if ($seller != null) {
            return $this->redirectToRoute('seller.index');
        }

        // Création d'une nouvelle demande de vendeur
        $seller = new Seller();
        $form = $this->createForm(SellerType::class, $seller);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seller = $form->getData();

            $seller->setStatus('pending')
                ->setUser($this->getUser());

            $manager->persist($seller);
            $manager->flush();

            // Réccuperer la demande qu'on vient d'ajouter
            $newSeller = $repository->findOneBy(['user' => $this->getUser()]);

            // Création d'une nouvelle notification pour l'administrateur
            $adminNotification = new AdminNotification();

            // Création du contenu de la notification
            $adminNotification->setContent($newSeller->getCompanyName() .' '. 
            'a soumis une nouvelle demande de vérification vendeur professionnel.')
            ->setIsRead(false)
            ->setSeller($newSeller);

            // Sauvegarde de la notification
            $manager->persist($adminNotification);
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
    public function annonceNew(
        Request $request,
        EntityManagerInterface $manager
    ): Response {

        // Un utilisateur non connecté ne peut pas créer une annonce
        if (!$this->getUser()) {
            return $this->redirectToRoute('home.index');
        }

        // Empêcher un vendeur non validé de créer une annonce
        if ($this->getUser()->getRoles()[0] != 'ROLE_SELLER') {
            return $this->redirectToRoute('seller.index');
        }

        $vehicle = new Vehicle();
        $form = $this->createForm(VehicleType::class, $vehicle);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehicle = $form->getData();

            $manager->persist($vehicle);
            $manager->flush();

            return $this->redirectToRoute('seller.index');
        }

        return $this->render('pages/seller/annonce_new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/vendeur/notification', 'seller.notification.index', methods: ['GET'])]
    public function notificationIndex(UserNotificationRepository $repository,
    SellerRepository $sellerRepository): Response
    {
        // Un utilisateur non connecté ne peut voir ses notifications
        if(!$this->getUser()) {
            return $this->redirectToRoute('home.index');
        }
        
        // Un utilisateur qui n'a pas envoyé une demande vendeur
        // ne peut voir le notification pour vendeur
        $seller = $sellerRepository->findOneBy(['user' => $this->getUser()]);
        if($seller == null) {
            return $this->redirectToRoute('home.index');
        }

        // Récuperer le nombre de notification non lue de l'utilisateur actuel.
        $userNotifications = $repository->findBy(['user' => $this->getUser()]);
        $userNotificationsNonLue = $repository->count([
            'user' => $this->getUser(),
            'isRead' => false
        ]);

        return $this->render('pages/seller/notification_index.html.twig', [
            'userNotifications' => $userNotifications,
            'userNotificationsNonLue' => $userNotificationsNonLue
        ]);
    }

    /**
     * Permet de marquer une notification comme lue
    *
    * @param UserNotification $userNotification
    * @param EntityManagerInterface $manager
    * @return Response
    */
    #[Route('/vendeur/confirmation-notification/{id}', 'seller.notification.confirm', methods: ['GET'])]
    public function notificationConfirm(UserNotification $userNotification,
    EntityManagerInterface $manager): Response
    {
        // Mettre la notification comme lue
        $userNotification->setIsRead(true);
        $manager->persist($userNotification);
        $manager->flush();

        return $this->redirectToRoute('seller.notification.index');
    }

    /**
     * Permet de supprimer une notification
    *
    * @param UserNotification $userNotification
    * @param EntityManagerInterface $manager
    * @return Response
    */
    #[Route('/vendeur/suppression-notification/{id}', 'seller.notification.delete', methods: ['GET'])]
    public function notificationDelete(UserNotification $userNotification,
    EntityManagerInterface $manager): Response
    {
        // Supprimer la notification
        $manager->remove($userNotification);
        $manager->flush();

        return $this->redirectToRoute('seller.notification.index');
    }
}
