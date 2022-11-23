<?php

namespace App\Form;

use App\Entity\Creneau\Creneau;
use App\Entity\Event\Event;
use App\Entity\Event\Registration;
use App\Repository\Creneau\CreneauRepository;
use App\Repository\Event\EventRepository;
use http\Client\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationHelpFormType extends AbstractType
{
    public function __construct(Private readonly EventRepository $eventRepository)
    { }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child:'name', type: TextType::class, options: [
                'label' => 'Prénom + Nom :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold'
                ],
                'attr' => [
                    'placeholder' => 'Bruce Wayne',
                    'class' => 'w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre Nom et Prénom.'),
                ],
            ])

            ->add(child:'email', type: EmailType::class, options: [
                'label' => 'Email :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold'
                ],
                'attr' => [
                    'placeholder' => 'batman@goham.city',
                    'class' => 'w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre Email.'),
                    new Email(message: 'Cette adresse Email n\'est pas valide.'),
                ],
            ])

            ->add(child:'phone',type: TextType::class, options: [
                'label' => 'Téléphone :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold'
                ],
                'attr' => [
                    'placeholder' => '06 66 66 66 66',
                    'type' => 'tel',
                    'class' => 'w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre Téléphone.'),
                ],
            ])

            ->add(child: 'activity', type: ChoiceType::class, options: [
                'placeholder' => 'Choisissez une activité',
                'label' => 'Une préférence ?',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold'
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'choices' => [
                    'Installation' => 'Installation',
                    'Vente' => 'Vente',
                    'Rangement' => 'Rangement',
                ],
                'required' => false
            ])

            ->add(child:'message',type: TextareaType::class, options: [
                'label' => 'Votre message :',
                'required' => false,
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold'
                ],
                'attr' => [
                    'placeholder' => 'Un petit message pour nous, mais ce n\'est pas obligatoire ! ❤️',
                    'class' => 'w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out',
                    'rows' => 3

                ]
            ])

            // add creneau available for this event
            ->add(child:'creneauChoices', type: EntityType::class, options: [
                'class' => Event::class,
                'expanded' => false,
                'multiple' => true,
                'label' => 'Choisissez un créneau :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold'
                ],
                'attr' => [
                    'class' => 'w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out'
                ],
                'choice_label' => 'event',
                'query_builder' => function (eventRepository $eventRepository) {
                    // Récupérer mon URL
                    $url = $_SERVER['REQUEST_URI'];
                    // Extraire de mon URL tout ce qui se trouve après /evenement/
                    preg_match('/\/evenement\/(.*)\//', $url, $matches);
                    $slug = $matches[1];
                    // A partir de mon slug, récupérer l'event
                    $slug = preg_replace('/\/.*/', '', $slug);
                    // Récupérer l'événement
                    $event = $this->eventRepository->findOneBy(['slug' => $slug]);
                    // Obtenir tous les créneaux de l'événement
                    return $eventRepository->createQueryBuilder('events')
                        ->andWhere('events.slug = :event')
                        ->setParameter(':event', $event)
                        ->orderBy('events.startsAt', 'ASC');
                }
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
