<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResponseEmailFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'response', type: TextareaType::class, options: [
                'label' => false,

                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'placeholder' => 'Votre message ici',
                    'rows' => 5,
                    'class' => 'w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out',
                ],
                'required' => false,
            ])

            ->add(child: 'submit', type: SubmitType::class, options: [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'w-full btn-purple-degrade mt-12',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
