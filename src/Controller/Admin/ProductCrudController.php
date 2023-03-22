<?php

namespace App\Controller\Admin;

use App\Entity\Product\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductCrudController extends AbstractCrudController
{
    public function __construct(private readonly string $productUploadDir)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(pageName: 'index', actionNameOrObject: 'detail');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(label: 'Produit')
            ->setEntityLabelInPlural(label: 'Produits')
            ->setPageTitle(pageName: 'index', title: '🛍️ Administration de la boutique')
            ->setPaginatorPageSize(maxResultsPerPage: 20)
            ->setDateTimeFormat(
                dateFormatOrPattern: dateTimeField::FORMAT_LONG,
                timeFormat: dateTimeField::FORMAT_SHORT
            )
            ->setPageTitle(
                pageName: 'detail',
                title: fn (Product $product) => '📇 Fiche produit - ' . $product->getName()
            )
            ->setPageTitle(
                pageName: 'edit',
                title: fn (Product $product) => '✍️ Modification - ' . $product->getName()
            )
            ->setPageTitle(
                pageName: 'new',
                title: '🛒 Ajouter un nouveau produit '
            )
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new(propertyName: 'id', label: 'ID')
            ->onlyOnIndex()
        ;

        yield TextField::new(propertyName: 'imageFile', label: 'Télécharger une image')
            ->setFormType(formTypeFqcn: VichImageType::class)
            ->onlyOnForms()
            ->setColumns(cols: 'col-12 col-sm-6')
        ;

        yield ImageField::new(propertyName: 'imageName', label: 'Image')
            ->setBasePath($this->productUploadDir)
            ->setUploadDir($this->getParameter(name: 'product_upload_dir'))
            ->onlyOnIndex()
            ->setColumns(cols: 'col-12 col-sm-6')
        ;


        yield BooleanField::new(propertyName: 'published', label: 'Publié')
            ->setColumns(cols: 'col-12 col-sm-6')
        ;

        yield TextField::new(propertyName: 'name', label: 'Nom')
            ->setColumns(cols: 'col-12 col-sm-6')
        ;

        yield SlugField::new(propertyName: 'slug')->setTargetFieldName(fieldName: 'name')
            ->onlyOnForms()
            ->setColumns(cols: 'col-12 col-sm-6')
        ;

        yield TextEditorField::new(propertyName: 'description', label: 'Description')
            ->setColumns(cols: 'col-12 col-sm-12')
        ;

        yield DateField::new(propertyName: 'startsAt', label: 'Début réservation')
            ->setColumns(cols: 'col-4 col-sm-4')
        ;

        yield DateField::new(propertyName: 'finishAt', label: 'Fin réservation')
            ->setColumns(cols: 'col-4 col-sm-4')
        ;

        yield DateField::new(propertyName: 'deliveryAt', label: 'Livré le')
            ->setFormat(dateFormatOrPattern: 'dd/MM/yyyy')
            ->setColumns(cols: 'col-4 col-sm-4')
        ;

        yield MoneyField::new(propertyName: 'price', label: 'Prix')
            ->setCurrency(currencyCode: 'EUR')
            ->setColumns(cols: 'col-4 col-sm-4')
        ;

        yield IntegerField::new(propertyName: 'stock', label: 'Stock')
            ->setColumns(cols: 'col-4 col-sm-4')
        ;

        yield DateTimeField::new(propertyName: 'createdAt', label: 'Créé ou modifié le')
            ->setFormat(dateFormatOrPattern: 'dd/MM/yyyy à HH:mm')
            ->onlyOnDetail()
            ->setColumns(cols: 'col-12 col-sm-6')
        ;

        yield IntegerField::new(propertyName: 'reserved', label: 'Réservé')
            ->onlyOnIndex()
            ->setColumns(cols: 'col-12 col-sm-6')
            ->addCssClass(cssClass: 'bg-secondary text-dark text-center border-end border-secondary')
        ;

        yield BooleanField::new(propertyName: 'deliveredSchool', label: 'Livré à l\'école')
            ->onlyOnIndex()
            ->setColumns(cols: 'col-12 col-sm-6')
        ;
    }
}
