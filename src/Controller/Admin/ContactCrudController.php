<?php

namespace App\Controller\Admin;

use App\Entity\Contact\Contact;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

#[IsGranted('ROLE_PRESIDENT', message: 'Désolé, seul le Président a accès à cette section.')]
class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        /** @noinspection PhpParamsInspection */
        return $crud
            ->setEntityLabelInSingular(label: 'Demande de contact')

            ->setEntityLabelInPlural(label: 'Demandes de contact')

            ->setPageTitle(pageName: 'index', title: 'Aperp - Administration des demandes de contact')

            ->setPaginatorPageSize(maxResultsPerPage: 20)
            // Ajout WYSIWYG pour le message (pas utile pour le moment)

            ->addFormTheme(themePath: '@FOSCKEditor/Form/ckeditor_widget.html.twig')

            ->setPageTitle(
                pageName: 'detail',
                title: fn (Contact $contact): ?string => $contact->getFullName()
            )

            ->setDateTimeFormat(
                dateFormatOrPattern: dateTimeField::FORMAT_LONG,
                timeFormat: dateTimeField::FORMAT_SHORT
            )
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new(propertyName: 'id')
            ->onlyOnIndex()
        ;

        yield TextField::new(propertyName: 'fullname', label: 'Nom complet');

        yield TextField::new(propertyName: 'email', label: 'Email');

        yield TextareaField::new(propertyName: 'message', label: 'Message')
            ->hideOnIndex()
            ->setFormType(formTypeFqcn: CKEditorType::class);

        yield DateTimeField::new(propertyName: 'createdAt', label: 'Date de réception')
            ->hideOnForm()
        ;


        yield BooleanField::new(propertyName: 'isReplied', label: 'Répondu ?')
            ->renderAsSwitch(isASwitch: false)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $email_response = Action::new(name: 'response', label: 'Répondre', icon: 'fa fa-file-invoice')
            ->linkToCrudAction(crudActionName: 'responseToEmail')
            ->addCssClass(cssClass: 'btn btn-primary')
        ;

        return $actions
            ->remove(pageName: Crud::PAGE_INDEX, actionName: Action::NEW)

            ->remove(pageName: Crud::PAGE_INDEX, actionName: Action::EDIT)

            ->remove(pageName: Crud::PAGE_DETAIL, actionName: Action::EDIT)

            ->disable(Action::NEW, Action::DELETE)

            ->add(pageName: Crud::PAGE_INDEX, actionNameOrObject: 'detail')

            // display if not replied



//            ->add(pageName: Crud::PAGE_DETAIL, actionNameOrObject: $email_response)

            ->setPermission(actionName: 'contact', permission: 'ROLE_PRESIDENT')
        ;
    }

    public function reply(AdminContext $adminContext, EntityManagerInterface $entityManager): void
    {
        $contact = $adminContext->getEntity()->getInstance();

        if (!$contact instanceof Contact) {
            throw new \LogicException(message: 'Entity is missing or not a Contact');
        }

        $contact->setIsReplied(isReplied: true);

        $entityManager->flush();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(
                propertyNameOrFilter: BooleanFilter::new(
                    propertyName: 'isReplied',
                    label: 'Afficher les messages répondu'
                )
            )
        ;
    }

    // responseToEmail open new page with form to send email
    public function responseToEmail(): Response
    {
        return $this->render(view: 'admin/contact/response.html.twig');
    }
}
