<?php

namespace App\Form;

use App\Entity\Event\Registration;
use Doctrine\DBAL\Types\ArrayType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationHelpFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child:'name', type: TextType::class, options: [
                'label' => 'Prénom + Nom :',
                'label_attr' => [
                    'class' => 'leading-7 text-sm text-gray-600'
                ],
                'attr' => [
                    'placeholder' => 'Bruce Wayne',
                    'class' => 'w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ]
            ])

            ->add(child:'email', type: EmailType::class, options: [
                'label' => 'Email :',
                'label_attr' => [
                    'class' => 'leading-7 text-sm text-gray-600'
                ],
                'attr' => [
                    'placeholder' => 'batman@goham.city',
                    'class' => 'w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ]
            ])

            ->add(child:'phone',type: TextType::class, options: [
                'label' => 'Téléphone :',
                'label_attr' => [
                    'class' => 'leading-7 text-sm text-gray-600'
                ],
                'attr' => [
                    'placeholder' => '06 66 66 66 66',
                    'class' => 'w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ]
            ])

            // ->add(child: 'event')
            ->add(child: 'howHeard', type: ChoiceType::class, options: [
                'placeholder' => 'Choisir une option',
                'label' => 'Comment avez-vous entendu parler de cet événement ?',
                'label_attr' => [
                    'class' => 'leading-7 text-sm text-gray-600'
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'choices' => [
                    'Twitter' => 'Twitter',
                    'Facebook' => 'Facebook',
                    'École' => 'École',
                    'Amis' => 'Amis',
                    'Autre' => 'Autre',
                ]
            ])

            ->add(child:'message',type: TextareaType::class, options: [
                'label' => 'Votre message :',
                'required' => false,
                'label_attr' => [
                    'class' => 'leading-7 text-sm text-gray-600'
                ],
                'attr' => [
                    'placeholder' => 'Un petit message pour nous, mais ce n\'est pas obligatoire ! ❤️',
                    'class' => 'w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
        ]);
    }
}
