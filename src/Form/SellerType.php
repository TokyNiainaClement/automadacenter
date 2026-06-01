<?php

namespace App\Form;

use App\Entity\Seller;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
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
                    new Assert\NotBlank(),
                    new Assert\Length(min: 3, max: 50)
                ]
            ])
            ->add('phoneNumber', TelType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
					rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: +261 34 00 000 00'
                ],
                'label' => 'numéro de téléphone *',
                'label_attr' => [
                    'class' => 'uppercase mt-6'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 13, max: 13)
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
                    new Assert\NotBlank(),
                    new Assert\Email()
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
                'constraints' => new Assert\NotBlank()
            ])
            ->add('documentFile', VichFileType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333]
						        rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]'
                ],
                'label' => 'pièce justificative requise (nif/stat/kbis) *',
                'label_attr' => [
                    'class' => 'uppercase mt-6'
                ],
                'constraints' => new Assert\File()
            ])
            ->add('logoFile', VichImageType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333]
						        rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]'
                ],
                'label' => 'Photo / logo de l\'entreprose *',
                'label_attr' => [
                    'class' => 'uppercase mt-6'
                ],
                'constraints' => new Assert\Image()
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
