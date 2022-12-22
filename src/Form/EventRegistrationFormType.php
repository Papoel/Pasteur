<?php

namespace App\Form;

use App\Entity\Event\RegistrationEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add(child: 'firstname', type: TextType::class, options: [
                'label' => 'Prénom :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'placeholder' => 'Bruce Wayne',
                    'class' => 'w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base 
                        outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out
                    ',
                ],
                'required' => false,
            ])

            ->add(child: 'lastname', type: TextType::class, options: [
                'label' => 'Nom :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'placeholder' => 'Bruce Wayne',
                    'class' => 'w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base 
                        outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out
                    ',
                ],
                'required' => false,
            ])

            ->add(child: 'telephone', type: TextType::class, options: [
                'label' => 'Téléphone :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'placeholder' => '06 66 66 66 66',
                    'type' => 'tel',
                    'class' => 'w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base 
                        outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out',
                ],
                'required' => false,
            ])

            ->add(child: 'email', type: EmailType::class, options: [
                'label' => 'Email :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'placeholder' => 'batman@goham.city',
                    'class' => 'w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base 
                        outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out
                    ',
                ],
                'required' => false,
            ])

            ->add(child: 'children', type: CollectionType::class, options: [
                'allow_add' => true,
                'allow_delete' => true,
                'entry_type' => AddChildrenFormType::class,
                'by_reference' => true,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegistrationEvent::class,
        ]);
    }
}
