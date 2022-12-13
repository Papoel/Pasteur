<?php

namespace App\Controller\Admin;

use App\Entity\Event\Event;
use App\Entity\Event\Registration;
use App\Repository\Event\EventRepository;
use App\Repository\Event\RegistrationRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationCrudController extends AbstractCrudController
{
    public function __construct(
        private EventRepository $eventRepository,
        private RegistrationRepository $registrationRepository
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Registration::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDateTimeFormat(
                dateFormatOrPattern: dateTimeField::FORMAT_LONG,
                timeFormat: dateTimeField::FORMAT_SHORT
            )

            ->setPageTitle(
                pageName: 'detail',
                title: fn (Event $event) => 'Fiche événement - ' . $event->getName()
            )

            ->setFormOptions([
                'validation_groups' => ['Default']
            ])
        ;
    }
    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new(propertyName: 'event', label: 'Événement');

        yield IdField::new(propertyName: 'id')
            ->onlyOnIndex()
        ;

        yield TextField::new(propertyName: 'name', label: 'Nom Complet');

        yield EmailField::new(propertyName: 'email', label: 'Email');

        yield TelephoneField::new(propertyName: 'telephone', label: 'Téléphone');

        yield ChoiceField::new(propertyName: 'activity', label: 'Activité proposée')
            ->setChoices([
                'Vente' => 'Vente' ,
                'Installation' => 'Installation' ,
                'Rangement' => 'Rangement' ,
            ])
            ->allowMultipleChoices()
            ->renderAsBadges([
                'Vente' => 'success' ,
                'Installation' => 'info' ,
                'Rangement' => 'dark' ,
            ]);

        yield TextareaField::new(propertyName: 'message');


        $event = $this->eventRepository->findOneBy(['id' => 3]);
        $creneauxForEvent = $event->getCreneaux()->toArray();

        yield ArrayField::new(propertyName: 'creneauChoices', label: 'Créneaux')
            ->formatValue(callable: function (EventRepository $eventRepository, Event $event) {
                return $eventRepository->findCreneaux($event->getId());
            })
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $data_creneaux_for_event = Action::new(name: 'creneauChoices', label: 'Charger les créneaux')
            ->linkToCrudAction(crudActionName: 'LoadCreneauxForEvent')
            ->addCssClass(cssClass: 'btn btn-danger')
            ->displayIf(static function ($entity) {
                return !$entity->getName();
            });

        return $actions
            ->add(Crud::PAGE_NEW ,$data_creneaux_for_event);
    }

    public function LoadCreneauxForEvent(Request $request): Response
    {
        dd($request);

        return $this->forward(controller: 'App\Controller\Admin\Event\LoadCreneauxByEvent::index');
        // return $this->redirectToRoute(route: 'app_response_message');
        // return $this->render(view: 'admin/contact/response.html.twig');
    }
}
