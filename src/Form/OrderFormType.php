<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            ->add(child: 'firstname', type: TextType::class, options: [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white',
                ],
                'label_attr' => [
                    'class' => 'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre prénom',
                    ]),
                    new Length(
                        [
                            'min' => 3,
                            'minMessage' => 'Votre prénom doit comporter au moins {{ limit }} caractères.',
                            // max length allowed by Symfony for security reasons
                            'max' => 80,
                            'maxMessage' => 'Votre prénom ne peut pas comporter plus de {{ limit }} caractères.',
                        ]
                    ),
                ],
            ])

            ->add(child: 'lastname', type: TextType::class, options: [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white',
                ],
                'label_attr' => [
                    'class' => 'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2',
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre nom',
                    ]),
                    new Length(
                        [
                            'min' => 3,
                            'minMessage' => 'Votre nom doit comporter au moins {{ limit }} caractères.',
                            // max length allowed by Symfony for security reasons
                            'max' => 80,
                            'maxMessage' => 'Votre nom ne peut pas comporter plus de {{ limit }} caractères.',
                        ]
                    ),
                ],
            ])

            ->add(child: 'email', type: EmailType::class, options: [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Email',
                    'class' => 'appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white',
                ],
                'label_attr' => [
                    'class' => 'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2',
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre adresse email',
                    ]),
                    new Email([
                        'message' => 'Veuillez renseigner une adresse email valide',
                    ]),
                ],
            ])

            ->add(child: 'telephone', type: TextType::class, options: [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Numéro de téléphone',
                    'class' => 'appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white',
                ],
                'label_attr' => [
                    'class' => 'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2',
                ],
                'required' => false,
                'constraints' => [
                    new Length(
                        [
                            'min' => 10,
                            'minMessage' => 'Votre numéro de téléphone doit comporter au moins {{ limit }} caractères.',
                            // max length allowed by Symfony for security reasons
                            'max' => 10,
                            'maxMessage' => 'Votre numéro de téléphone ne peut pas comporter plus de {{ limit }} caractères.',
                        ]
                    ),
                ],
            ])

            // add submit button
            ->add(child: 'submit', type: SubmitType::class, options: [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'ml-1',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => array(),
            // 🚥 commenter pour réactiver la validation html5 !
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }
}
