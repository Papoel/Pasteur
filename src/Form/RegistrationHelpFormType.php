<?php

namespace App\Form;

use App\Entity\Event\Registration;
use App\Repository\Event\EventRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationHelpFormType extends AbstractType
{
    public function __construct(private readonly EventRepository $eventRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'name', type: TextType::class, options: [
                'label' => 'Prénom + Nom :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'placeholder' => 'Bruce Wayne',
                    'class' => 'w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out',
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
                    'class' => 'w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out',
                ],
                'required' => false,
            ])
            ->add(child: 'phone', type: TextType::class, options: [
                'label' => 'Téléphone :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'placeholder' => '06 66 66 66 66',
                    'type' => 'tel',
                    'class' => 'w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out',
                ],
                'required' => false,
            ])
            ->add(child: 'activity', type: ChoiceType::class, options: [
                'placeholder' => 'Choisissez une activité',
                'label' => 'Une préférence ?',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out',
                ],
                'choices' => [
                    'Installation' => 'Installation',
                    'Vente' => 'Vente',
                    'Rangement' => 'Rangement',
                ],
                'required' => false,
            ])
            ->add(child: 'message', type: TextareaType::class, options: [
                'label' => 'Votre message :',
                'required' => false,
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'placeholder' => 'Un petit message pour nous, mais ce n\'est pas obligatoire ! ❤️',
                    'class' => 'w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out',
                    'rows' => 3,
                ],
            ])
            ->add(child: 'creneauChoices', type: ChoiceType::class, options: [
                'label' => 'Quel créneau ?',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded border border-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out',
                ],
                'choices' => $options['event_creneaux'],

                'choice_label' => function ($choice, $key, $value) {
                    return $choice->getStartsAt()->format('H:i').' - '.$choice->getEndsAt()->format('H:i');
                },
                'choice_value' => function ($choice) {
                    return $choice->getId();
                },
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'mapped' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
            'event_creneaux' => [],
        ]);
        $resolver->setAllowedTypes(option: 'event_creneaux', allowedTypes: 'array');
    }
}
