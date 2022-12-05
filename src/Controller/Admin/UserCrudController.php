<?php

namespace App\Controller\Admin;

use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
            ->setPageTitle(pageName: 'index', title: 'Aperp - Administration des utilisateurs')
            ->setPaginatorPageSize(maxResultsPerPage: 20)
            ->setPageTitle(
                pageName: 'detail',
                title: fn (User $user) => $user->getFullName()
            )
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new(propertyName: 'id')->onlyOnIndex(),

            TextField::new(propertyName: 'fullname', label: 'Nom Complet')->onlyOnIndex(),

            TextField::new(propertyName: 'firstname', label: 'Prénom')->hideOnIndex(),

            TextField::new(propertyName: 'lastname', label: 'Nom')->hideOnIndex(),

            TextField::new(propertyName: 'pseudo', label: 'Pseudo'),

            TextField::new(propertyName: 'email', label: 'Email')->hideOnIndex(),

            TextField::new(propertyName: 'password', label: 'Mot de passe')->onlyWhenCreating(),

            ChoiceField::new(propertyName: 'roles')->setChoices(
                [
                    'PRESIDENT'      => 'ROLE_PRESIDENT',
                    'ADMINISTRATEUR' => 'ROLE_ADMIN',
                    'MEMBRE'         => 'ROLE_USER',
                ]
            )->allowMultipleChoices()->hideOnIndex(),

            DateTimeField::new(propertyName: 'createdAt', label: 'Date de création')->onlyOnDetail(),

            TextField::new(propertyName: 'telephone', label: 'Téléphone'),

            TextField::new(propertyName: 'address', label: 'Adresse')->hideOnIndex(),

            TextField::new(propertyName: 'complement_address', label: 'Complément d\'adresse')->hideOnIndex(),

            TextField::new(propertyName: 'postal_code', label: 'Code postal')->hideOnIndex(),

            TextField::new(propertyName: 'town', label: 'Ville')->hideOnIndex(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!$this->IsGranted(attribute: 'ROLE_PRESIDENT')) {
            return $actions
                ->remove(pageName: Crud::PAGE_INDEX, actionName: Action::NEW)
                ->remove(pageName: Crud::PAGE_INDEX, actionName: Action::EDIT)
                ->add(pageName: Crud::PAGE_INDEX, actionNameOrObject: 'detail')
                ->remove(pageName: Crud::PAGE_DETAIL, actionName: Action::EDIT)
                ->disable(Action::NEW, Action::DELETE);
        }

        return $actions
            ->add(pageName: Crud::PAGE_INDEX, actionNameOrObject: 'detail');
    }
}
