<?php

namespace App\Controller\Admin;

use App\Entity\Contact\Contact;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_PRESIDENT', message: 'Désolé, seul le Président a accès à cette section.')]
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
            ->setPageTitle(pageName: 'index', title: 'Aperp - Administration des demandes de contact')
            ->setPaginatorPageSize(maxResultsPerPage: 20)
            // Ajout WYSIWYG pour le message (pas utile pour le moment)
            // ->addFormTheme(themePath: '@FOSCKEditor/Form/ckeditor_widget.html.twig');
            ->setPageTitle(
                pageName: 'detail',
                title: fn (Contact $contact): ?string => $contact->getFullName()
            )
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new(propertyName: 'id')->onlyOnIndex(),

            TextField::new(propertyName: 'fullname', label: 'Nom complet'),

            TextField::new(propertyName: 'email', label: 'Email'),

            TextareaField::new(propertyName: 'message', label: 'Message')
                ->setFormType(formTypeFqcn: CKEditorType::class),

            DateTimeField::new(propertyName: 'createdAt', label: 'Date de réception')->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        // Cette action exécute la méthode 'responseToEmail()'..
        $email_response = Action::new(name: 'response', label: 'Répondre', icon: 'fa fa-file-invoice')
            // TODO: Créer la méthode responseToEmail() dans le contrôleur
            ->linkToCrudAction(crudActionName: 'responseToEmail')
            ->addCssClass(cssClass: 'btn btn-primary')
        ;

        return $actions
            ->remove(pageName: Crud::PAGE_INDEX, actionName: Action::NEW)
            ->remove(pageName: Crud::PAGE_INDEX, actionName: Action::EDIT)
            ->add(pageName: Crud::PAGE_INDEX, actionNameOrObject: 'detail')
            ->remove(pageName: Crud::PAGE_DETAIL, actionName: Action::EDIT)
            ->disable(Action::NEW, Action::DELETE)
            ->add(Crud::PAGE_DETAIL, $email_response)
            ->setPermission(actionName: 'contact', permission: 'ROLE_PRESIDENT')
        ;
    }

    public function reply(AdminContext $adminContext, EntityManagerInterface $entityManager)
    {
        $contact = $adminContext->getEntity()->getInstance();

        if (!$contact instanceof Contact) {
            throw new \LogicException('Entity is missing or not a Contact');
        }

        $contact->setIsReplied(isReplied: true);

        $entityManager->flush();
    }
}
