<?php

namespace App\Form\User;

use App\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'email', type: EmailType::class, options: [
                'label' => 'Email :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded border-b focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'required' => false,
            ])

            ->add(child: 'firstname', type: TextType::class, options: [
                'label' => 'Prénom :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded border-b focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'required' => false,
            ])

            ->add(child: 'lastname', type: TextType::class, options: [
                'label' => 'Nom :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded border-b focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'required' => false,
            ])

            ->add(child: 'pseudo', type: TextType::class, options: [
                'label' => 'Pseudo :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded border-b focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'required' => false,
            ])

            ->add(child: 'telephone', type: TextType::class, options: [
                'label' => 'Téléphone :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'type' => 'tel',
                    'class' => 'w-full bg-white rounded border-b focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'required' => false,
            ])

            ->add(child: 'address', type: TextareaType::class, options: [
                'label' => 'Adresse :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold border-0',
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded border-b focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'required' => false,
            ])

            ->add(child: 'complementAddress', type: TextareaType::class, options: [
                'label' => 'Complément d\'adresse :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded border-b focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'required' => false,
            ])

            ->add(child: 'postalCode', type: TextType::class, options: [
                'label' => 'Code postal :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded border-b focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'required' => false,
            ])

            ->add(child: 'town', type: TextType::class, options: [
                'label' => 'Ville :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded border-b focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'required' => false,
            ])

            ->add(child: 'birthday', type: DateType::class, options: [
                'label' => 'Date de naissance :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded border-b py-1 px-3 
                        text-base text-gray-700 leading-8 
                        focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 
                        outline-none transition-colors duration-200 ease-in-out',
                    'min' => '1900-01-01',
                    'max' => date(format: 'Y-m-d'),
                ],
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
            ]);

            // Si l'utilisateur est en train de créer un nouveau compte,
            // ajouter les champs de mot de passe
            if ($options['is_new']) {
                $builder->add(child: 'plainPassword', type: RepeatedType::class, options: [
                    'type' => PasswordType::class,
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'label_attr' => [
                            'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                        ],
                        'attr' => [
                            'class' => 'w-full bg-pink-700 rounded border-b focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                        ],
                    ],
                    'second_options' => [
                        'label' => 'Confirmer le mot de passe',
                        'label_attr' => [
                            'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                        ],
                        'attr' => [
                            'class' => 'w-full bg-pink-700 rounded border-b focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                        ],
                    ],
                    'invalid_message' => 'Les mots de passe doivent être identique',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        new Length(
                            [
                                'min' => 8,
                                'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères.',
                                // max length allowed by Symfony for security reasons
                                'max' => 80,
                                'maxMessage' => 'Votre mot de passe ne peut pas comporter plus de {{ limit }} caractères.',
                            ]
                        ),
                        new Regex(pattern: [
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/',
                            'message' =>
                                'Votre mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre',
                        ]),
                    ],
                ]);
            }

            /*->add(child: 'askDeleteAccountAt', type: DateType::class, options: [
                'label' => 'demande de suppression du compte :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'disabled' => 'disabled',
                    'class' => 'w-full bg-gray-200 rounded border-b focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'input' => 'datetime_immutable',
            'attr' => ['novalidate' => 'novalidate'],
            'is_new' => false,
        ]);
    }
}
