<?php

namespace App\Form;

use App\Entity\Event\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('location')
            ->add('price')
            ->add('startsAt')
            ->add('finishAt')
            ->add('status')
            ->add('capacity')
            ->add('imageFileName')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('helpNeeded')
            ->add('slug')
            // findCreneaux from EventRepository return choiceType with creneaux
            /*->add($builder
                ->add('creneaux', CreneauFormType::class, ['by_reference' => false])
                ->add('creneau', TextType::class)
            )*/
            ->add('creneaux')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
