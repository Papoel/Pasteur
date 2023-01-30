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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
            ->setPageTitle(pageName: 'index', title: '💌 Liste des messages reçus 💌')
            ->setPaginatorPageSize(maxResultsPerPage: 20)
            // Ajout WYSIWYG pour le message (pas utile pour le moment)

            ->addFormTheme(themePath: '@FOSCKEditor/Form/ckeditor_widget.html.twig')
            ->setDateTimeFormat(
                dateFormatOrPattern: dateTimeField::FORMAT_LONG,
                timeFormat: dateTimeField::FORMAT_SHORT
            )
            ->setPageTitle(
                pageName: 'detail',
                title: fn (Contact $contact): ?string => $contact->getFullName()
            )
            ->setPageTitle(
                pageName: 'detail',
                title: fn (Contact $contact) => sprintf(
                    '📬 <i>Message reçu de </i> <b>%s</b>.',
                    $contact->getFullName()
                )
            )
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new(propertyName: 'id')
            ->onlyOnIndex();

        yield TextField::new(propertyName: 'fullname', label: 'Nom complet');

        yield TextField::new(propertyName: 'subject', label: 'Sujet');

        yield TextField::new(propertyName: 'email', label: 'Email')
            ->hideOnIndex();

        yield TextareaField::new(propertyName: 'message', label: 'Message')
            ->hideOnIndex();

        yield DateTimeField::new(propertyName: 'createdAt', label: 'Date de réception')
            ->hideOnForm();

        yield BooleanField::new(propertyName: 'isReplied', label: 'Répondu ?')
            ->renderAsSwitch(isASwitch: false);

        // display if the contact has been replied
        if (Crud::PAGE_DETAIL === $pageName) {
            yield DateTimeField::new(propertyName: 'replyAt', label: 'Date de réponse')
                ->hideOnForm();
        }

        yield TextareaField::new(propertyName: 'response', label: 'La réponse de l\'APE :')
            ->onlyOnDetail();
    }

    public function configureActions(Actions $actions): Actions
    {
        $email_response = Action::new('response', label: 'Répondre par email')
            ->linkToCrudAction(crudActionName: 'responseToEmail')
            ->addCssClass(cssClass: 'btn btn-primary')
            ->displayIf(static function ($entity) {
                return !$entity->getIsReplied();
            });

        /*$email_response = Action::new(name: 'response', label: 'Répondre', icon: 'fa fa-file-invoice')
            ->linkToCrudAction(crudActionName: 'responseToEmail')
            ->addCssClass(cssClass: 'btn btn-primary')
        ;*/

        return $actions
            ->remove(pageName: Crud::PAGE_INDEX, actionName: Action::NEW)
            ->remove(pageName: Crud::PAGE_INDEX, actionName: Action::EDIT)
            ->remove(pageName: Crud::PAGE_DETAIL, actionName: Action::EDIT)
            ->add(pageName: Crud::PAGE_INDEX, actionNameOrObject: 'detail')
            ->add(pageName: Crud::PAGE_DETAIL, actionNameOrObject: $email_response)

            ->setPermission(actionName: 'contact', permission: 'ROLE_SUPER_ADMIN');
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
            );
    }

    // responseToEmail open new page with form to send email
    public function responseToEmail(Request $request): Response
    {
        $queryString = $request->getQueryString();

        parse_str(string: $queryString, result: $params);
        $entityId = $params['entityId'];

        return $this->forward(controller: 'App\Controller\Admin\Contact\EmailResponseController::index', path: [
            'entityId' => $entityId,
        ]);
    }
}
