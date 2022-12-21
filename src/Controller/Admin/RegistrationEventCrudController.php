<?php

namespace App\Controller\Admin;

use App\Entity\Event\Children;
use App\Entity\Event\RegistrationEvent;
use App\Form\AddChildrenFormType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RegistrationEventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RegistrationEvent::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDateTimeFormat(
                dateFormatOrPattern: dateTimeField::FORMAT_LONG,
                timeFormat: dateTimeField::FORMAT_SHORT
            )

            ->setPageTitle(
                pageName: 'detail',
                title: fn (RegistrationEvent $registrationEvent) => 'Inscription - ' . $registrationEvent
                        ->getEvent()
                        ->getName()
            )

            ->setFormOptions([
                'validation_groups' => ['Default']
            ])

        ;
        return Crud::new();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(pageName: Crud::PAGE_INDEX, actionNameOrObject: 'detail');
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new(propertyName: 'id')
            ->onlyOnIndex()
        ;
        yield TextField::new(propertyName: 'firstname', label: 'Prénom');

        yield TextField::new(propertyName: 'lastname', label: 'Nom');

        yield EmailField::new(propertyName: 'email', label: 'Email');

        yield TelephoneField::new(propertyName: 'telephone', label: 'Téléphone');

        yield AssociationField::new(propertyName: 'event', label: 'Événement')
            ->setCrudController(crudControllerFqcn: EventCrudController::class)
        ;

        /*yield FormField::addPanel(label: 'Inscrire des enfants')
            ->collapsible()
            ->setIcon(iconCssClass: 'fa fa-info')
            ->setHelp(help: 'Ajouter des enfants')*/
        ;

        /*yield CollectionField::new(propertyName: 'children', label: 'Enfants inscrits')
            ->setEntryIsComplex(isComplex: true)
            ->setEntryType(formTypeFqcn: ChildrenFormType::class)
            ->setTemplatePath(path: 'admin/registration/add_children.html.twig')
        ;*/

        yield CollectionField::new(propertyName: 'children', label: 'Enfants')
            ->allowAdd()
            ->setEntryType(formTypeFqcn: AddChildrenFormType::class)
            ->setEntryIsComplex()
            ->setTemplatePath(path: 'admin/registration/add_children.html.twig')
        ;

        yield BooleanField::new(propertyName: 'paid', label: 'Payé')
            ->renderAsSwitch(isASwitch: false)
        ;
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        $slug = $entityInstance->getEvent()->getSlug();

        if (!$entityInstance instanceof RegistrationEvent) {
            return;
        }

        $entityInstance->setEvent($entityInstance->getEvent());

        parent::persistEntity($em, $entityInstance);
    }
}
