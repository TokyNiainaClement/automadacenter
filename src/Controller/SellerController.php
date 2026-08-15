<?php

namespace App\Controller;

use App\Entity\AdminNotification;
use App\Entity\Seller;
use App\Entity\UserNotification;
use App\Entity\Vehicle;
use App\Entity\VehicleImage;
use App\Form\SellerType;
use App\Form\VehicleImageType;
use App\Form\VehicleType;
use App\Repository\SellerRepository;
use App\Repository\UserNotificationRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Component\Pager\PaginatorInterface;

final class SellerController extends AbstractController
{
    /**
     * This controller display the seller dashboard
     *
     * @param SellerRepository $repository
     * @return Response
     */
    #[Route('/vendeur', 'seller.index', methods: ['GET'])]
    public function index(
        SellerRepository $repository,
        UserNotificationRepository $userNotificationRepository
    ): Response {
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

        // Un utilisateur non connecté ne peut pas créer s'inscrire en vendeur
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

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
            $adminNotification->setContent($newSeller->getCompanyName() . ' ' .
                'a soumis une nouvelle demande de vérification vendeur professionnel.')
                ->setTitle('Demande d\'inscription vendeur')
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
     * This controller allow us to display the lists of vehicles
    *
    * @param SellerRepository $sellerRepository
    * @param VehicleRepository $vehicleRepository
    * @return Response
    */
    #[Route('/vendeur/annonces', 'annonce.index', methods: ['GET'])]
    public function annonceIndex(
        SellerRepository $sellerRepository,
        VehicleRepository $vehicleRepository, 
        PaginatorInterface $paginator, Request $request): Response
    {
        // Vérifier que l'utilisateur est bien connécté
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        // Un vendeur non validé n'a pas de liste d'annonces
        if (!in_array('ROLE_SELLER', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('seller.index');
        }

        // Récuperer les vehicule à l'aide du profile vendeur.
        $seller = $sellerRepository->findOneBy(['user' => $this->getUser()]);

        $vehicles = $paginator->paginate(
            $vehicleRepository->findAnnonces($seller),
            $request->query->getInt('page', 1), /* Nombre de page */
            6 /* Limite par page */
        );

        return $this->render('pages/seller/annonce_index.html.twig', [
            'vehicles' => $vehicles
        ]);
    }

    /**
     * This controller allow the seller to create a new annonce
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/vendeur/annonce/creation', 'annonce.new', methods: ['GET', 'POST'])]
    public function annonceNew(
        Request $request,
        EntityManagerInterface $manager,
        SellerRepository $sellerRepository
    ): Response {

        // Un utilisateur non connecté ne peut pas créer une annonce
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        // Empêcher un vendeur non validé de créer une annonce
        if (!in_array('ROLE_SELLER', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('seller.index');
        }

        // Réccuperer le profile vendeur de l'utilisateur en cours
        $seller = $sellerRepository->findOneBy(['user' => $this->getUser()]);

        $vehicle = new Vehicle();
        $form = $this->createForm(VehicleType::class, $vehicle);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehicle = $form->getData();

            // Puis associer le vendeur à la nouvelle annonce crée
            $vehicle->setSeller($seller);
            $vehicle->setStatus("active");

            // Sauvegarde de l'annonce
            $manager->persist($vehicle);
            $manager->flush();

            return $this->redirectToRoute('seller.index');
        }

        return $this->render('pages/seller/annonce_new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/vendeur/detail-annonce-{id}', 'annonce.show', methods: ['GET'])]
    public function annonceShow(
        Vehicle $vehicle
    ): Response
    {
        return $this->render('pages/seller/annonce_show.html.twig', [
            'vehicle' => $vehicle
        ]);
    }

    #[Route('/vendeur/modification-annonce-{id}', 'annonce.edit', methods: ['GET', 'POST'])]
    public function annonceEdit(Vehicle $vehicle, Request $request, 
    EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(VehicleType::class, $vehicle);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $vehicle = $form->getData();

            $manager->persist($vehicle);
            $manager->flush();

            $this->addFlash(
                'success',
                'Modification de l\'annonce éfféctué.'
            );

            return $this->redirectToRoute('annonce.index');
        }

        return $this->render('pages/seller/annonce_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/vendeur/modifier-status-annonce-{id}', 'annonce.edit.status', methods: ['GET'])]
    public function setStatusAnnonce(Vehicle $vehicle,
    EntityManagerInterface $manager): Response
    {
        if($vehicle->getStatus() == 'active')
        {
            $vehicle->setStatus('paused');
            $manager->persist($vehicle);
            $manager->flush();
        }
        else {
            $vehicle->setStatus('active');
            $manager->persist($vehicle);
            $manager->flush();
        }
        return $this->redirectToRoute('annonce.index');
    }

    #[Route('/vendeur/supprimer-annonce-{id}', 'annonce.delete', methods: ['GET'])]
    public function annonceDelete(Vehicle $vehicle,
    EntityManagerInterface $manager): Response
    {
        $manager->remove($vehicle);
        $manager->flush();

        $this->addFlash(
                'success',
                'Suppression de l\'annonce éfféctué.'
        );

        return $this->redirectToRoute('annonce.index');

    }

    /**
     * This controller allow to add images for a vehicle
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/vendeur/image-annonce/creation', 'annonce.image.new', methods: ['GET', 'POST'])]
    public function imageNew(
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        // Un utilisateur non connecté ne peut pas ajouter des images à une annonce
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        // Empêcher un vendeur non validé d'ajouter des images à une annonce
        if (!in_array('ROLE_SELLER', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('seller.index');
        }

        $vehicleImage = new VehicleImage();
        $form = $this->createForm(VehicleImageType::class, $vehicleImage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vehicle = $vehicleImage->getVehicle();

            $imageFiles = $form->get('vehicleImageFile')->getData();

            foreach ($imageFiles as $imageFile) {
                $image = new VehicleImage();

                $image->setVehicle($vehicle);
                $image->setVehicleImageFile($imageFile);
                $manager->persist($image);
            }

            $manager->flush();

            return $this->redirectToRoute('seller.index');
        }

        return $this->render('pages/seller/annonce_image_new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/vendeur/notification', 'seller.notification.index', methods: ['GET'])]
    public function notificationIndex(
        UserNotificationRepository $repository,
        SellerRepository $sellerRepository
    ): Response {
        // Un utilisateur non connecté ne peut voir ses notifications
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        // Un utilisateur qui n'a pas envoyé une demande vendeur
        // ne peut voir le notification pour vendeur
        $seller = $sellerRepository->findOneBy(['user' => $this->getUser()]);
        if ($seller == null) {
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
    public function notificationConfirm(
        UserNotification $userNotification,
        EntityManagerInterface $manager
    ): Response {
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
    public function notificationDelete(
        UserNotification $userNotification,
        EntityManagerInterface $manager
    ): Response {
        // Supprimer la notification
        $manager->remove($userNotification);
        $manager->flush();

        return $this->redirectToRoute('seller.notification.index');
    }

}
