<?php

namespace App\Form;

use App\Entity\Contact\Contact;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'fullname', type: TextType::class, options: [
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
            ->add(child: 'subject', type: TextType::class, options: [
                'label' => 'Sujet :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'placeholder' => 'Sujet du message (facultatif)',
                    'class' => 'w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out',
                ],
                'required' => false,
            ])
            ->add(child: 'message', type: TextareaType::class, options: [
                'label' => 'Message :',
                'label_attr' => [
                    'class' => 'block text-gray-500 uppercase tracking-wider text-sm font-bold',
                ],
                'attr' => [
                    'placeholder' => 'Votre message ici',
                    'class' => 'w-full bg-white rounded focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out',
                ],
                'required' => false,
            ])
            ->add(child: 'submit', type: SubmitType::class, options: [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn-purple-degrade rounded text-lg py-1 px-3 block ml-3 ',
                ],
            ])
            ->add(child: 'captcha', type: Recaptcha3Type::class, options: [
                'constraints' => new Recaptcha3(),
                'action_name' => 'contact',
                'locale' => 'fr',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
