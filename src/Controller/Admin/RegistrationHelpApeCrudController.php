<?php

namespace App\Controller\Admin;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationHelp;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RegistrationHelpApeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RegistrationHelp::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(label: 'Événement')
            ->setEntityLabelInPlural(label: 'Membre de l\'APE apportant leur aide à un événement')

            ->setPageTitle(pageName: 'index', title: '💼 Aperp - Organisation interne des événements')

            ->setDateTimeFormat(
                dateFormatOrPattern: dateTimeField::FORMAT_LONG,
                timeFormat: dateTimeField::FORMAT_SHORT
            )

            ->setPageTitle(
                pageName: 'detail',
                title: fn (Event $event) => 'Fiche inscription - ' . $event->getName()
            )

            ->setFormOptions([
                'validation_groups' => ['Default'],
            ])
            ;
    }

    // Displayed fields in the list view
    public function configureFields(string $pageName): iterable
    {
        // 	id, event_id, name, email, telephone, activity, message, creneau_choices
        yield IdField::new(propertyName: 'id')->hideOnForm();
        yield TextField::new(propertyName: 'name', label: 'Nom');
        yield TextField::new(propertyName: 'telephone', label: 'Téléphone');
        yield TextField::new(propertyName: 'activity', label: 'Activité');
        yield TextField::new(propertyName: 'message', label: 'Message')
            ->hideOnIndex();
        ;


    }


}
