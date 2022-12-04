<?php

namespace App\Controller\Admin;

use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
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
                'detail',
                fn (User $user) => $user->getFullName()
            )
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new(propertyName: 'id')->onlyOnIndex(),
            TextField::new(propertyName: 'firstname', label: 'Prénom'),
            TextField::new(propertyName: 'lastname', label: 'Nom'),
            TextField::new(propertyName: 'pseudo', label: 'Pseudo'),
            TextField::new(propertyName: 'email', label: 'Email'),
            TextField::new(propertyName: 'password', label: 'Mot de passe')->hideOnIndex()->hideOnDetail(),
            ChoiceField::new(propertyName: 'roles')->setChoices(
                [
                    'PRESIDENT' => 'ROLE_PRESIDENT',
                    'ADMINISTRATEUR' => 'ROLE_ADMIN',
                    'UTILISATEUR' => 'ROLE_USER',
                ]
            )->allowMultipleChoices()->onlyOnDetail(),

            DateTimeField::new(propertyName: 'createdAt')->onlyOnDetail()
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
