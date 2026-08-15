<?php

namespace App\Form;

use App\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class VehicleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentYear = (int) date('Y');
        $years = array_combine(range($currentYear, 1980), range($currentYear, 1980));

        $fuel_types = ['Essence', 'Diesel', 'Hybride', 'Électrique'];
        $fuel_typesArray = array_combine($fuel_types, $fuel_types);

        $trasmissions = ['Manuelle', 'Automatique', 'Semi-automatique'];
        $trasmissions_array = array_combine($trasmissions, $trasmissions);

        $vehicleConditions = ['comme neuf', 'bon état général', 'excellent', 'très bon état', 'à réparer'];
        $vehicleConditionsArray = array_combine($vehicleConditions, $vehicleConditions);

        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Titre de l\'annonce'
                ],
                'label' => 'Titre *',
                'label_attr' => [
                    'class' => 'uppercase'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "Le titre est obligatoire."),
                    new Assert\Length(
                        min: 10,
                        max: 255,
                        minMessage: "Le titre doit contenir au moins {{ limit }} caractères.",
                        maxMessage: "Le titre ne doit pas dépasser {{ limit }} caractères."
                    )
                ],
            ])
            ->add('price', MoneyType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: 95 000 000'
                ],
                'label' => 'Prix en MGA *',
                'label_attr' => [
                    'class' => 'uppercase'
                ],
                'currency' => false,
                'constraints' => [
                    new Assert\NotNull(message: "Le prix est obligatoire."),
                    new Assert\Positive(message: "Le prix doit être positif.")
                ]
            ])
            ->add('brand', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: Toyota Hilux Revo Double Cabine'
                ],
                'label' => 'Marque *',
                'label_attr' => [
                    'class' => 'uppercase'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "La marque est obligatoire."),
                    new Assert\Length(
                        min: 2,
                        minMessage: "La marque doit contenir au moins {{ limit }} caractères."
                    )
                ]
            ])
            ->add('model', TextType::class, [
                'attr' => [
                    'class' =>  'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Model de votre vehicule'
                ],
                'label' => 'Modèle *',
                'label_attr' => [
                    'class' => 'uppercase'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "Le model est obligatoire."),
                    new Assert\Length(
                        min: 2,
                        minMessage: "Le model doit contenir au moins {{ limit }} caractères."
                    )
                ]
            ])
            ->add('year', ChoiceType::class, [
                'choices' => $years,
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Séléctionnez une année.'
                ],
                'label' => 'Année *',
                'label_attr' => [
                    'class' => 'uppercase'
                ],
                'constraints' => [
                    new Assert\NotNull(message: "L'année de fabrication est obligatoire."),
                    new Assert\Range(
                        min: 1980,
                        max: $currentYear,
                        notInRangeMessage: "L'année doit être comprise entre {{ limit }} et {{ limit }}."
                    )
                ]
            ])
            ->add('mileage', IntegerType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: 82 000 Km'
                ],
                'label' => 'Kilomètrage *',
                'label_attr' => [
                    'class' => 'uppercase'
                ],
                'constraints' => [
                    new Assert\NotNull(message: "Le kilomètrage est obligatoire."),
                    new Assert\GreaterThanOrEqual(
                        0,
                        message: "Le kilomètrage doit être positif."
                    )
                ]
            ])
            ->add('fuelType', ChoiceType::class, [
                'choices' => $fuel_typesArray,
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: diesel, essence'
                ],
                'label' => 'Carburant *',
                'label_attr' => [
                    'class' => 'uppercase'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "Le type carburant est obligatoire."),
                    new Assert\Choice(
                        choices: $fuel_typesArray,
                        message: "Veuillez choisir un carburant valide."
                    )
                ]
            ])
            ->add('transmission', ChoiceType::class, [
                'choices' => $trasmissions_array,
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: Automatique, manuelle, etc'
                ],
                'label' => 'Boîte de vitesse *',
                'label_attr' => [
                    'class' => 'uppercase'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "Le type de boîte de vitesse est obligatoire."),
                    new Assert\Choice(
                        choices: $trasmissions_array,
                        message: "Veuillez choisir un type de boîte de vitesse valide."
                    )
                ]
            ])
            ->add('color', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Couleur du véhicule'
                ],
                'label' => 'Couleur *',
                'label_attr' => [
                    'class' => 'uppercase'
                ],
                'constraints' => new Assert\NotBlank(message: "La couleur est obligatoire.")
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'w-full bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Description du véhicule'
                ],
                'label' => 'Description *',
                'label_attr' => [
                    'class' => 'mt-4 uppercase'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "La déscription est obligatoire."),
                    new Assert\Length(
                        min: 30,
                        minMessage: "La déscription doit contenir au moins {{ limit }} caractères."
                    )
                ]
            ])
            ->add('vehicleCondition', ChoiceType::class, [
                'choices' => $vehicleConditionsArray,
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: En bonne état, excellent, etc'
                ],
                'label' => 'État du véhicule *',
                'label_attr' => [
                    'class' => 'mt-4 uppercase'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "L'état du véhicule est obligatoire."),
                    new Assert\Choice(
                        choices: $vehicleConditionsArray,
                        message: "Veuillez choisir l'état de votre véhicule."
                    )
                ]
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'class' => 'bg-[#111111] border border-[#333] 
                    rounded-xl px-4 py-3 mt-2 outline-none focus:border-[#F97316]',
                    'placeholder' => 'Ex: Dr Rue Raseta Antananarivo'
                ],
                'label' => 'Localisation *',
                'label_attr' => [
                    'class' => 'mt-4 uppercase'
                ],
                'constraints' => [
                    new Assert\NotBlank(message: "La localisation est obligatoire."),
                    new Assert\Length(
                        min: 3,
                        minMessage: "La localisation doit contenir au moins {{ limit }} caractères."
                    )
                ]
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
