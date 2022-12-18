<?php

declare(strict_types=1);


namespace App\Form;

use App\Entity\Event\RegistrationEvent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChildrenFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder , array $options): void
    {
        $builder
            ->add(child: 'firstname', type: TextType::class, options: [
                'label' => 'Prénom',
                'mapped' => false
            ])

            ->add(child: 'lastname', type: TextType::class, options: [
                'label' => 'Nom',
                'mapped' => false
            ])

            ->add(child: 'classroom', type: TextType::class, options: [
                'label' => 'Classe',
                'mapped' => false
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
