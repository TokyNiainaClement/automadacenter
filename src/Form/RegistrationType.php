<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'attr' => [
                    'class' => 'w-full bg-[#111111] border border-[#333] focus:border-[#F97316] 
                    rounded-lg px-4 py-3 outline-none',
                    'placeholder' => 'Rakoto Jean'
                ],
                'label' => 'Nom complet',
                'label_attr' => [
                    'class' => 'text-sm text-gray-400 block mb-2'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 3, max: 50)
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'w-full bg-[#111111] border border-[#333] focus:border-[#F97316] 
                    rounded-lg px-4 py-3 outline-none',
                    'placeholder' => 'Adresse email'
                ],
                'label' => 'Adresse email',
                'label_attr' => [
                    'class' => 'text-sm text-gray-400 block mb-2'
                ],
                'constraints' => [
                    new Assert\Email(),
                    new Assert\Length(min: 2, max: 180)
                ]
            ])
            ->add('plaintextPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'class' => 'w-full bg-[#111111] border border-[#333] focus:border-[#F97316] 
                        rounded-lg px-4 py-3 outline-none',
                        'placeholder' => '********'
                    ],
                    'label' => 'Mot de passe',
                    'label_attr' => [
                        'class' => 'text-sm text-gray-400 block mb-2'
                    ],

                ],
                'second_options' => [
                    'attr' => [
                        'class' => 'w-full bg-[#111111] border border-[#333] focus:border-[#F97316] 
                        rounded-lg px-4 py-3 outline-none',
                        'placeholder' => '********'
                    ],
                    'label' => 'Confirmation du mot de passe',
                    'label_attr' => [
                        'class' => 'text-sm text-gray-400 block mb-2'
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'constraints' => new Assert\NotBlank()
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'w-full bg-[#F97316] hover:bg-[#ea6500] transition-colors py-3 rounded-lg font-bold'
                ],
                'label' => 'Créer mon compte'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
