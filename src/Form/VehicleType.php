<?php

namespace App\Form;

use App\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Form\Type\VichImageType;

class VehicleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Titre de l\'annonce'
                ],
                'label' => 'Titre',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 10, max: 255)
                ]
            ])
            ->add('price', MoneyType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: 75 000 000 MGA'
                ],
                'label' => 'Prix',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Positive()
                ]
            ])
            ->add('brand', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: Toyota Hilux Revo Double Cabine'
                ],
                'label' => 'Marque',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 3, max: 100)
                ]
            ])
            ->add('model', TextType::class, [
                'attr' => [
                    'class' =>  'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: Type 01'
                ],
                'label' => 'Modèle',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 3, max: 100)
                ]
            ])
            ->add('year', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: 2019'
                ],
                'label' => 'Année',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\LessThan(2027)
                ]
            ])
            ->add('mileage', IntegerType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: 82 000 Km'
                ],
                'label' => 'Kilomètrage',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\LessThan(100000)
                ]
            ])
            ->add('fuelType', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: diesel, essence'
                ],
                'label' => 'Carburant',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 3, max: 50)
                ]
            ])
            ->add('transmission', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: Automatique, manuelle, etc'
                ],
                'label' => 'Boîte de vitesse',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 5, max: 50)
                ]
            ])
            ->add('color', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: Gris, noir, etc'
                ],
                'label' => 'Couleur',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 3, max: 50)
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'w-full bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Description du véhicule'
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'mt-4'
                ],
                'constraints' => new Assert\NotBlank()
            ])
            ->add('vehicleCondition', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: En bonne état, excellent, etc'
                ],
                'label' => 'État du véhicule',
                'label_attr' => [
                    'class' => 'mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 5, max: 50)
                ]
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316',
                    'placeholder' => 'Ex: Antananarivo, Toamasina, etc'
                ],
                'label' => 'Localisation',
                'label_attr' => [
                    'class' => 'mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 3, max: 100)
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'attr' => [
                    'class' => 'border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316'
                ],
                'label' => 'Photo',
                'label_attr' => [
                    'class' => 'mt-4'
                ],
                'constraints' => new Assert\Image()
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'bg-[#F97316] hover:bg-[#ea6500] px-8 py-3 mt-5 
                    rounded-xl font-bold'
                ],
                'label' => 'Publier l\'annonce'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicle::class,
        ]);
    }
}
