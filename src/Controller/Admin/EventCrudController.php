<?php

namespace App\Controller\Admin;

use App\Entity\Event\Event;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EventCrudController extends AbstractCrudController
{
    public const EVENT_BASE_PATH = 'public/uploads/images/events/';
    public const EVENT_UPLOAD_DIR = 'assets/uploads/images/events/';

    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new(propertyName: 'id')->hideOnForm();

        yield TextField::new(propertyName: 'name', label: 'Titre de l\'événement')
            ->setColumns(cols: 'col-4')
        ;

        yield TextField::new(propertyName: 'location', label: 'Se déroulera à')
            ->hideOnIndex()
            ->setColumns(cols: 'col-4')
        ;

        yield MoneyField::new(propertyName: 'price', label: 'Prix')
            ->setCurrency(currencyCode: 'EUR')
            ->setCustomOption(optionName: 'storedAsCents', optionValue: false)
            ->setColumns(cols: 'col-4')
        ;

        yield DateTimeField::new(propertyName: 'startsAt', label: 'Commence')
            ->renderAsChoice()
            ->setColumns(cols: 'col-6')
        ;

        yield DateTimeField::new(propertyName: 'finishAt', label: 'Fini')
            ->hideOnIndex()
            ->renderAsChoice()
            ->setColumns(cols: 'col-6')
        ;

        yield BooleanField::new(propertyName: 'helpNeeded', label: 'Besoin d\'Aide ?')
            ->setColumns(cols: 'md-col-2')
        ;

        yield TextEditorField::new(propertyName: 'description', label: 'Décrivez l\'événement')
            ->hideOnIndex()
            ->setColumns(cols: 'col-12')
            ->setCssClass(cssClass: 'uppercase')
        ;

        // display the price without /100 in the admin panel with a euro sign

        yield IntegerField::new(propertyName: 'capacity', label: 'Nombre de places maximales')
            ->hideOnIndex()
            ->addCssClass(cssClass: 'text-primary text-uppercase')
            ->setColumns(cols: 'col-2')
        ;

        yield AssociationField::new(propertyName: 'creneaux', label: 'Créneaux')
            ->hideOnIndex()
            ->setColumns(cols: 'col-4');
    }
}
