<?php

namespace App\Controller\Admin;

use App\Entity\User\User;
use App\EventSubscriber\AdminPasswordHashSubscriber;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(label: 'Utilisateur')

            ->setEntityLabelInPlural(label: 'Utilisateurs')

            ->setPageTitle(pageName: 'index', title: '🔥 Aperp - Administration des utilisateurs')

            ->setPaginatorPageSize(maxResultsPerPage: 20)

            ->setPageTitle(
                pageName: 'detail',
                title: fn (User $user) => '📇 Fiche contact - ' . $user->getFullName()
            )

            ->setPageTitle(
                pageName: 'edit',
                title: fn (User $user) => '️️✍️ Modification - ' . $user->getFullName()
            )

            ->setPageTitle(
                pageName: 'new',
                title: 'Ajouter un membre à l\'APERP 🎉'
            )

            ->setDateTimeFormat(
                dateFormatOrPattern: dateTimeField::FORMAT_LONG,
                timeFormat: dateTimeField::FORMAT_SHORT
            )

        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new(propertyName: 'id')->onlyOnIndex();

        yield FormField::addPanel(label: 'Informations Générales')->setIcon(iconCssClass: 'fa fa-info');

        yield TextField::new(propertyName: 'fullname', label: 'Nom Complet')
            ->onlyOnIndex()
        ;

        yield TextField::new(propertyName: 'firstname', label: 'Prénom')
            ->hideOnIndex()
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield TextField::new(propertyName: 'lastname', label: 'Nom')
            ->hideOnIndex()
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield TextField::new(propertyName: 'pseudo', label: 'Pseudo')
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield TextField::new(propertyName: 'email', label: 'Email')
            //->hideOnIndex()
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield ChoiceField::new(propertyName: 'roles')
            ->setChoices(choiceGenerator: [
                'PRESIDENT' => 'ROLE_PRESIDENT',
                'SECRETAIRE' => 'ROLE_SECRETAIRE',
                'TRESORIER' => 'ROLE_TRESORIER',
                'MEMBRE ACTIF' => 'ROLE_MEMBRE',
                'WEBMASTER' => 'ROLE_WEBMASTER',
            ])
            ->allowMultipleChoices()
            ->renderAsBadges([
                'ROLE_PRESIDENT' => 'danger',
                'ROLE_WEBMASTER' => 'success',
                'ROLE_USER' => 'primary',
                'ROLE_SECRETAIRE' => 'info',
                'ROLE_TRESORIER' => 'warning',
                'ROLE_MEMBRE' => 'secondary'
            ])
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield TextField::new(propertyName: 'telephone', label: 'Téléphone')
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield DateField::new(propertyName: 'birthday', label: 'Date de naissance')
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        /* yield TextField::new(propertyName: 'password', label: 'Mot de passe')
             ->hideOnIndex()
             ->setColumns(cols: 'col-12 col-md-6')
         ;*/

        yield DateTimeField::new(propertyName: 'createdAt', label: 'Date de création')
            ->onlyOnDetail()
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield TextField::new(propertyName: 'address', label: 'Adresse')
            ->hideOnIndex()
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield TextField::new(propertyName: 'postal_code', label: 'Code postal')
            ->hideOnIndex()
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield TextField::new(propertyName: 'town', label: 'Ville')
            ->hideOnIndex()
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield TextField::new(propertyName: 'complement_address', label: 'Complément d\'adresse')
            ->hideOnIndex()
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        if ($pageName == 'new') {
            yield FormField::addPanel(label: 'Mot de passe')->setIcon(iconCssClass: 'fa fa-key');
        }

        yield Field::new(propertyName: 'password', label: 'Confirmation du mot de passe')
            ->onlyWhenCreating()
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'required' => true,
            ])
            ->setColumns(cols: 'col-12 col-sm-4')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!$this->IsGranted(attribute: 'ROLE_PRESIDENT')) {
            return $actions
                ->remove(pageName: Crud::PAGE_INDEX, actionName: Action::NEW)
                ->remove(pageName: Crud::PAGE_INDEX, actionName: Action::EDIT)
                ->disable(Action::NEW, Action::DELETE);
        }

        return $actions
            ->add(pageName: Crud::PAGE_INDEX, actionNameOrObject: 'detail');
    }
}
