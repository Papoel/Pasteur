<?php

namespace App\Controller\Admin;

use App\Entity\Event\RegistrationEvent;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RegistrationEventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RegistrationEvent::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new(propertyName: 'id')
            ->onlyOnIndex()
        ;
        yield TextField::new(propertyName: 'firstname');
        yield TextField::new(propertyName: 'lastname');
        yield EmailField::new(propertyName: 'email');
        yield TelephoneField::new(propertyName: 'telephone');
        yield AssociationField::new(propertyName: 'event', label: 'Événement');
        yield FormField::addPanel(label: 'Inscrire des enfants')
            ->collapsible()
            ->setIcon(iconCssClass: 'fa fa-info')
            ->setHelp(help: 'Ajouter des enfants');
        ;
        yield ArrayField::new('children');


    }

}
