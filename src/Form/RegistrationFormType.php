<?php

namespace App\Form;

use App\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'email', type: EmailType::class, options: [
                'label' => 'Votre adresse email',
                'label_attr' => [
                    'class' => 'text-xs uppercase tracking-wider text-gray-700',
                ],
            ])

            ->add(child: 'plainPassword', type: RepeatedType::class, options: [
                'type' => PasswordType::class,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'first_options' => [
                    'label' => 'Mot de passe',
                    'label_attr' => [
                        'class' => 'text-xs uppercase tracking-wider text-gray-700',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'label_attr' => [
                        'class' => 'text-xs uppercase tracking-wider text-gray-700',
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
            ])

            ->add(child: 'firstname', options: [
                'label' => 'Votre prénom',
                'label_attr' => [
                    'class' => 'text-xs uppercase tracking-wider text-gray-700',
                ],
            ])

            ->add(child: 'lastname', options: [
                'label' => 'Votre nom',
                'label_attr' => [
                    'class' => 'text-xs uppercase tracking-wider text-gray-700',
                ],
            ])

            ->add(child: 'agreeTerms', type: CheckboxType::class, options: [
                'mapped' => false,
                'label' => 'J\'accepte les conditions d\'utilisation',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez accepter les conditions d\'utilisation',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            // 🚥 commenter pour réactiver la validation html5 !
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }
}
