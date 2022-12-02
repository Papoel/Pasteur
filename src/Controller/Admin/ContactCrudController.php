<?php

namespace App\Controller\Admin;

use App\Entity\Contact\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(label: 'Demande de contact')
            ->setEntityLabelInPlural(label: 'Demandes de contact')

            ->setPageTitle(pageName: 'index', title: 'Aperp - Administartion des demandes de contact')
            ->setPaginatorPageSize(maxResultsPerPage: 20)

            ->addFormTheme(themePath: '@FOSCKEditor/Form/ckeditor_widget.html.twig')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new(propertyName: 'id')->onlyOnIndex(),
            TextField::new(propertyName: 'fullname', label: 'Nom complet'),
            TextField::new(propertyName: 'email', label: 'Email'),
            TextareaField::new(propertyName: 'message', label: 'Message')
                ->setFormType(formTypeFqcn: CKEditorType::class)
                ->hideOnIndex(),
            DateTimeField::new(propertyName: 'createdAt', label: 'Date de création')->hideOnForm(),
        ];
    }
}
