<?php

namespace App\Form;

use App\Entity\Vehicle;
use App\Entity\VehicleImage;
use App\Repository\SellerRepository;
use App\Repository\VehicleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class VehicleImageType extends AbstractType
{
    private TokenStorageInterface $token;
    private SellerRepository $repository;

    public function __construct(TokenStorageInterface $token, SellerRepository $repository)
    {
        $this->token = $token;
        $this->repository = $repository;
    }

    /**
     * This method allow us to find a seller object
     *
     * @param SellerRepository $repository
     * @return object|null
     */
    private function findSeller(SellerRepository $repository): ?object
    {
        return $repository->findOneBy(
            ['user' => $this->token->getToken()->getUser()]
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choice_label' => 'brand',
                'query_builder' => function (VehicleRepository $v) {
                    return $v->createQueryBuilder('v')
                    ->where('v.seller = :seller')
                    ->orderBy('v.brand', 'ASC')
                    ->setParameter('seller', $this->findSeller($this->repository));
                },
                'multiple' => false,
                'attr' => [
                    'class' => 'w-full bg-[#111111] border border-[#333] 
                rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]'
                ],
                'label' => 'Nom du véhicule de l\'annonce *',
                'label_attr' => [
                    'class' => 'mt-4 uppercase'
                ]
            ])
            ->add('vehicleImageFile', FileType::class, [
                'mapped' => false,
                'multiple' => true,
                'required' => true,
                'attr' => [
                    'class' => 'sr-only'
                ],
                'label' => 'Photos du véhicule *',
                'label_attr' => [
                    'class' => 'mt-4 uppercase'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "Veuillez séléctionner un ou plusieur photos."),
                    new Assert\All([
                        new Assert\File(
                            mimeTypes: ['image/jpg', 'image/png', 'image/jpeg', 'image/webp'],
                            mimeTypesMessage: "Veuillez importer une image PNG, JPG, JPEG ou WEBP.",
                            maxSize: "2M",
                            maxSizeMessage: "L'image ne doit pas dépasser 2M.",
                        )

                    ]),
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'bg-[#F97316] hover:bg-[#ea6500] px-8 py-3 mt-5 
                    rounded-xl font-bold'
                ],
                'label' => 'Ajouter'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VehicleImage::class,
        ]);
    }
}
