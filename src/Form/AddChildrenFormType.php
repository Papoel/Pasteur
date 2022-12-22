<?php

namespace App\Form;

use App\Entity\Event\Children;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddChildrenFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'firstname', type: TextType::class, options: [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add(child: 'lastname', type: TextType::class, options: [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add(child: 'classroom', type: ChoiceType::class, options: [
                'choices' => [
                    'CP' => 'CP',
                    'CE1' => 'CE1',
                    'CE2' => 'CE2',
                    'CM1' => 'CM1',
                    'CM2' => 'CM2',
                    'Extérieur' => 'Exterieur',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Children::class,
        ]);
    }
}
