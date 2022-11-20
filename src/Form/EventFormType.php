<?php

namespace App\Form;

use App\Entity\Event\Event;
use Symfony\Component\Form\AbstractType;
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
            ->add('thumbnail')
            ->add('plagesHoraires')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
