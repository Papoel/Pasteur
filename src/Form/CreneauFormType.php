<?php

namespace App\Form;

use App\Entity\Slot\Slot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreneauFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startsAt', DateTimeType::class, [
                'label' => 'Début',
                'label_attr' => ['class' => 'active'],
                'format' => 'HH:mm',
                'widget' => 'single_text',
                'attr' => ['data-field' => 'datetime'],
            ])

            ->add('endsAt', DateTimeType::class, [
                'label' => 'Fin',
                'label_attr' => ['class' => 'active'],
                'format' => 'dd-MM-yyyy HH:mm',
                'widget' => 'single_text',
                'attr' => ['data-field' => 'datetime'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Slot::class,
        ]);
    }
}
