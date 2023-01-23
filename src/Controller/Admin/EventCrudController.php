<?php

namespace App\Controller\Admin;

use App\Entity\Event\Event;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EventCrudController extends AbstractCrudController
{
    public const FULL = 'COMPLET';

    public function __construct(private readonly string $uploadDir)
    {
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(label: 'Événement')

            ->setEntityLabelInPlural(label: 'Événements')

            ->setPageTitle(pageName: 'index', title: '📆 Aperp - Administration des événements')

            ->setPaginatorPageSize(maxResultsPerPage: 20)

            ->setDateTimeFormat(
                dateFormatOrPattern: dateTimeField::FORMAT_LONG,
                timeFormat: dateTimeField::FORMAT_SHORT
            )

            ->setPageTitle(
                pageName: 'detail',
                title: fn (Event $event) => '📇 Fiche événement - ' . $event->getName()
            )

            ->setPageTitle(
                pageName: 'edit',
                title: fn (Event $event) => '✍️ Modification - ' . $event->getName()
            )

            ->setPageTitle(
                pageName: 'new',
                title: 'Organiser un nouvel événement 🎉'
            )

            ->setFormOptions([
                'validation_groups' => ['Default'],
            ])

        ;
    }

    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new(propertyName: 'id')->hideOnForm();

        yield TextField::new(propertyName: 'name', label: 'Titre de l\'événement')
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield SlugField::new(propertyName: 'slug', label: 'Slug')
            ->setTargetFieldName(fieldName: 'name')
            ->setUnlockConfirmationMessage('Il est recommandé de ne pas agir sur ce label, êtes-vous sûr de vous ?')
            ->setColumns(cols: 'col-12 col-sm-6')
            ->hideOnIndex()
        ;

        yield TextField::new(propertyName: 'location', label: 'Se déroulera à')
            ->hideOnIndex()
            ->setColumns(cols: 'col-12 col-sm-6')
        ;

        yield TextareaField::new(propertyName: 'description', label: 'Décrivez l\'événement')
            ->hideOnIndex()
            ->setColumns(cols: 'col-12')
        ;

        yield DateTimeField::new(propertyName: 'startsAt', label: 'Début')
            ->setColumns(cols: 'col-12 col-sm-4')
            ->renderAsNativeWidget()
        ;

        yield DateTimeField::new(propertyName: 'finishAt', label: 'Fini')
            ->hideOnIndex()
            ->setColumns(cols: 'col-12 col-sm-4')
            ->renderAsNativeWidget()
        ;

        yield TextField::new(propertyName: 'imageFile', label: 'Télécharger une image')
            ->setFormType(formTypeFqcn: VichImageType::class)
            ->onlyOnForms()
            ->setColumns(cols: 'col-12 col-sm-6')
        ;

        yield BooleanField::new(propertyName: 'published', label: 'Publié ?')
            ->setColumns(cols: 'col-12 col-sm-6')
        ;

        yield BooleanField::new(propertyName: 'helpNeeded', label: 'Besoin d\'Aide ?')
            ->setColumns(cols: 'col-12')
        ;

        yield AssociationField::new(propertyName: 'creneaux', label: 'Créneaux horaires pour l\'aide')
            ->setColumns(cols: 'col-8')
            ->formatValue(function ($value, $entity) {
                $str = $entity->getCreneaux()[0];
                for ($i = 1; $i < $entity->getCreneaux()->count(); ++$i) {
                    $str .= ' | ' . $entity->getCreneaux()[$i];
                }

                return $str;
            })
            ->hideOnIndex()
        ;

        yield MoneyField::new(propertyName: 'price', label: 'Prix')
            ->setCurrency(currencyCode: 'EUR')
            ->setCustomOption(optionName: 'storedAsCents', optionValue: true)
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield ChoiceField::new(propertyName: 'status')
            ->setChoices(choiceGenerator: [
                'PREPARATION' => 'PREPARATION',
                'EN COURS' => 'RUNNING',
                'TERMINÉ' => 'FINISHED',
            ])
            ->renderAsBadges(badgeSelector: [
                'PREPARATION' => 'warning',
                'RUNNING' => 'success',
                'FINISHED' => 'danger',
            ])
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        // if capacity == 0 then display 'COMPLET' with background red and text red on index
        yield IntegerField::new(propertyName: 'capacity', label: 'Places disponibles')
            ->setColumns(cols: 'col-12 col-sm-4')
            ->setCustomOption(optionName: 'fullColor', optionValue: 'red')
            ->setCustomOption(optionName: 'fullTextColor', optionValue: 'white')
            ->setCustomOption(optionName: 'full', optionValue: self::FULL)
            ->formatValue(function ($value, $entity) {
                if (0 === $entity->getCapacity()) {
                    return self::FULL;
                }
                return $value;
            })
        ;

        yield ImageField::new(propertyName: 'imageName', label: 'Image')
            ->setBasePath($this->uploadDir)
            ->hideOnForm()
            ->setColumns(cols: 'col-12')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(pageName: Crud::PAGE_INDEX, actionNameOrObject: 'detail');
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param                        $entityInstance
     *
     * @return void
     * Permet de vérifier si un événement du même nom existe déjà dans la base de données
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Ajouter la contrainte d'unicité de l'entité.
        $events = $entityManager->getRepository(Event::class)->findAll();
        $eventDetails = $entityInstance->getSlug() . $entityInstance->getStartsAt()->format('d-m-Y H:i:s');

        foreach ($events as $event) {
            if ($eventDetails === $event->getSlug() . $event->getStartsAt()->format('d-m-Y H:i:s')) {
                $this->addFlash(
                    type: 'danger',
                    message: 'Un événement du même nom existe déjà dans la base de données.'
                );

                return;
            }
        }

        // Vérifier si un événement du même nom avec la même date de début existe déjà dans la base de données.
        foreach ($events as $event) {
            if (
                $event->getName() === $entityInstance->getName() &&
                $event->getStartsAt() === $entityInstance->getStartsAt()
            ) {
                $this->addFlash(
                    type: 'danger',
                    message: 'Un événement du même nom avec la même date de début existe déjà dans la base de données.'
                );

                return;
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
}
