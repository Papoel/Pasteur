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

class RegistrationEventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RegistrationEvent::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(label: 'Inscription à un événement')

            ->setEntityLabelInPlural(label: 'Inscriptions aux événements')

            ->setPageTitle(pageName: 'index', title: '📆 Aperp - Inscriptions aux événements')

            ->setPaginatorPageSize(maxResultsPerPage: 20)

            ->setDateTimeFormat(
                dateFormatOrPattern: dateTimeField::FORMAT_LONG,
                timeFormat: dateTimeField::FORMAT_SHORT
            )

            ->setPageTitle(
                pageName: 'detail',
                title: fn(RegistrationEvent $registrationEvent) => '📇 Inscription - ' . $registrationEvent
                        ->getEvent()
                        ->getName()
            )

            ->setPageTitle(
                pageName: 'edit',
                title: fn (RegistrationEvent $registrationEvent) => sprintf(
                    '✍️ <i>Modification Inscription </i> <b>%s</b> (<i>%s</i>)',
                    $registrationEvent->getEvent()->getName(),
                    $registrationEvent->getFullname()
                )
            )

            ->setPageTitle(
                pageName: 'new',
                title: 'Organiser un nouvel événement 🎉'
            )

            ->setFormOptions([
                'validation_groups' => ['Default'] ,
            ])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(pageName: Crud::PAGE_INDEX, actionNameOrObject: 'detail');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new(propertyName: 'id')
            ->onlyOnIndex();

        yield TextField::new(propertyName: 'firstname', label: 'Prénom')
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield TextField::new(propertyName: 'lastname', label: 'Nom')
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield TelephoneField::new(propertyName: 'telephone', label: 'Téléphone')
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield EmailField::new(propertyName: 'email', label: 'Email')
            ->setColumns(cols: 'col-12 col-sm-6')
        ;

        yield AssociationField::new(propertyName: 'event', label: 'Événement')
            ->setColumns(cols: 'col-12 col-sm-4')
            ->setCrudController(crudControllerFqcn: EventCrudController::class)
        ;

        yield CollectionField::new(propertyName: 'children', label: 'Enfants')
            ->allowAdd()
            ->setEntryType(formTypeFqcn: AddChildrenFormType::class)
            ->setEntryIsComplex()
            ->setTemplatePath(path: 'admin/registration/add_children.html.twig');

        yield BooleanField::new(propertyName: 'paid', label: 'Payé')

            ->renderAsSwitch(isASwitch: false);
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if (!$entityInstance instanceof RegistrationEvent) {
            return;
        }

        $entityInstance->setEvent($entityInstance->getEvent());

        parent::persistEntity($em, $entityInstance);
    }
}
