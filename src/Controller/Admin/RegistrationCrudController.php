<?php

namespace App\Controller\Admin;

use App\Entity\Event\Registration;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RegistrationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Registration::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
