<?php

namespace App\Form;

use App\Entity\Seller;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class SellerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
				rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: Madagascar Auto Distribution'
                ],
                'label' => 'nom du concessionnaire / entreprise *',
                'label_attr' => [
                    'class' => 'uppercase'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "Le nom d'entreprise est obligatoire."),
                    new Assert\Length(
                        min: 3,
                        max: 50,
                        minMessage: "Le nom d'entreprise doit contenir au moins {{ limit }} catactères.",
                        maxMessage: "Le nom d'entreprise ne doit pas dépasser 50 caractères."
                    )
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
					rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: 034 00 000 00, 032 00 000 00'
                ],
                'label' => 'numéro de téléphone *',
                'label_attr' => [
                    'class' => 'uppercase'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "Le numéro téléphone est obligatoire."),
                    new Assert\Regex(
                        '/^(032|033|034|038)\d{7}$/',
                        message: "Le numéro téléphone est invalide."
                    )
                ]

            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] 
					border border-[#333] rounded-xl px-4 py-3 mt-2 outline-none 
                    focus:border-[#F97316]',
                    'placeholder' => 'Ex: contact@entreprise.mg'
                ],
                'label' => 'adresse email pro *',
                'label_attr' => [
                    'class' => 'uppercase mt-6'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "L'email est obligatoire."),
                    new Assert\Email(message: "Adresse email invalide.")
                ]
            ])
            ->add('adresse', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
					rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: Rue Dr. Raseta, Andraharo, Antananarivo'
                ],
                'label' => 'adresse physique du showroom *',
                'label_attr' => [
                    'class' => 'uppercase mt-6'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "L'adresse physique est obligatoire."),
                    new Assert\Length(
                        min: 5,
                        max: 255,
                        minMessage: "L'adresse doit contenir au moins 5 caractères.",
                        maxMessage: "L'adresse physique ne dois pas dépasser 255 caractères."
                    )
                ]
            ])
            ->add('documentFile', VichFileType::class, [
                'attr' => [
                    'class' => 'sr-only'
                ],
                'label' => 'pièce justificative requise (nif/stat/kbis) en PDF *',
                'label_attr' => [
                    'class' => 'uppercase mt-6'
                ],
                'required' => false,
                'allow_delete' => false,
                'constraints' => [
                    new Assert\NotBlank(message: "Le document justificatif est obligatoire."),
                    new Assert\File(
                        mimeTypes: ['application/pdf'],
                        mimeTypesMessage: 'Veuillez importer un fichier PDF.'
                    )
                ]
            ])
            ->add('logoFile', VichImageType::class, [
                'attr' => [
                    'class' => 'sr-only'
                ],
                'label' => 'Photo / logo de l\'entreprise *',
                'label_attr' => [
                    'class' => 'uppercase mt-6'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "Le logo de l'entreprise est obligatoire."),
                    new Assert\Image(
                        maxSize: "2M",
                        maxSizeMessage: "L'image ne doit pas dépasser 5M.",
                        mimeTypesMessage: "Veuillez importer une image PNG, JPG, JPEG ou WEBP."
                    )
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'bg-[#F97316] hover:bg-[#ea6500] px-8 py-3 mt-6 
				rounded-xl font-bold'
                ],
                'label' => 'Soumettre ma demande',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seller::class,
        ]);
    }
}
