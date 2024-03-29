<?php

namespace App\Controller\Admin;

use App\Entity\Contact\Contact;
use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use App\Entity\Event\RegistrationHelp;
use App\Entity\Order\Order;
use App\Entity\Product\Product;
use App\Entity\User\User;
use App\Repository\Contact\ContactRepository;
use App\Repository\Event\EventRepository;
use App\Repository\Event\RegistrationEventRepository;
use App\Repository\Order\OrderRepository;
use App\Repository\User\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly OrderRepository $orderRepository,
        private readonly EventRepository $eventRepository,
        private readonly ContactRepository $contactRepository,
        private readonly RegistrationEventRepository $registrationEventRepository,
    ) {
    }

    #[Route('/admin', name: 'admin')]
    #[isGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(
            attribute: 'ROLE_ADMIN',
            subject: "Accès à la section d'administration",
            message: 'Désolé, votre rôle ne vous donne pas accès a cette section.'
        );

        // TODO: Lister les anniversaires du mois
        // TODO; Trouver quelque chose de sympa pour les anniversaire du jour.

        return $this->render(view: 'admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle(title: 'APE Rousies Pasteur - Administration')
            ->renderContentMaximized()
            ->setLocales(locales: ['fr' => '🇫🇷 Français'])
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard(label: 'Dashboard', icon: 'fa fa-home');

        yield MenuItem::section();
        yield MenuItem::linkToUrl(label: 'Site-Web', icon: 'fas fa-check', url: $this->generateUrl(route: 'app_home'));

        // Utilisateurs
        $totalUsers = $this->userRepository->count([]);
        yield MenuItem::section(label: 'Utilisateurs', icon: 'fa fa-users')
            ->setBadge(content: $totalUsers, style: 'info')
        ;
        yield MenuItem::subMenu(label: 'Action', icon: 'fas fa-bars')->setSubItems(subItems: [
            MenuItem::linkToCrud(label: 'Voir les utilisateurs', icon: 'fas fa-eye', entityFqcn: User::class),
            MenuItem::linkToCrud(label: 'Ajouter un membre', icon: 'fas fa-plus', entityFqcn: User::class)
                ->setAction(actionName: Crud::PAGE_NEW),
        ]);

        // Événements
        $totalEvents = (string) $this->eventRepository->countNotPastEvents();
        yield MenuItem::section(label: 'Événements', icon: 'fas fa-jedi')
            ->setBadge($totalEvents, style: 'info')
        ;
        yield MenuItem::subMenu(label: 'Action', icon: 'fas fa-bars')->setSubItems(subItems: [
            MenuItem::linkToCrud(label: 'Voir les événements', icon: 'fas fa-eye', entityFqcn: Event::class),
            MenuItem::linkToCrud(label: 'Ajouter un événement', icon: 'fas fa-plus', entityFqcn: Event::class)
                ->setAction(actionName: Crud::PAGE_NEW),
        ]);

        // Inscriptions
        $totalInscriptions = $this->registrationEventRepository->count([]);
        yield MenuItem::section(label: 'Inscriptions', icon: 'fas fa-calendar-check')
            ->setBadge($totalInscriptions, style: 'info');
        yield MenuItem::subMenu(label: 'Action', icon: 'fas fa-bars')
            ->setSubItems(subItems: [
                MenuItem::linkToCrud(
                    label: 'Voir les inscriptions',
                    icon: 'fas fa-eye',
                    entityFqcn: RegistrationEvent::class
                ),
                MenuItem::linkToCrud(
                    label: 'Ajouter une inscription',
                    icon: 'fas fa-plus',
                    entityFqcn: RegistrationEvent::class
                )
                    ->setAction(actionName: Crud::PAGE_NEW),
                MenuItem::linkToRoute(label: 'Détails', icon: 'fas fa-boxes', routeName: 'app_admin_details_events'),
            ])
        ;

        // Messages (depuis la page de contact)
        if ($this->container->get('security.authorization_checker')->isGranted(attribute: 'ROLE_SUPER_ADMIN')) {
            $totalMessages = $this->contactRepository->count(['isReplied' => false]);
            yield MenuItem::section(label: 'Messages', icon: 'fas fa-envelope')
                ->setBadge($totalMessages, style: 'warning')
                ->setPermission(permission: 'ROLE_SUPER_ADMIN')
            ;
            yield MenuItem::subMenu(label: 'Action', icon: 'fas fa-bars')->setSubItems(subItems: [
                MenuItem::linkToCrud(label: 'Voir tous les messages', icon: 'fas fa-eye', entityFqcn: Contact::class)
                    ->setPermission(permission: 'ROLE_SUPER_ADMIN'),
            ]);
        }

        // Aide
        yield MenuItem::section(label: 'Aide', icon: 'fas fa-question-circle');
        yield MenuItem::subMenu(label: 'Action', icon: 'fas fa-bars')->setSubItems(subItems: [
            MenuItem::linkToCrud(label: 'Membres', icon: 'fas fa-users', entityFqcn: RegistrationHelp::class)
                ->setAction(actionName: Crud::PAGE_INDEX),
        ]);

        // Produits
        yield MenuItem::section(label: 'Produits', icon: 'fas fa-boxes');
        yield MenuItem::subMenu(label: 'Action', icon: 'fas fa-bars')
            ->setSubItems(subItems: [
                MenuItem::linkToCrud(label: 'Voir les produits', icon: 'fas fa-eye', entityFqcn: Product::class)
                    ->setAction(actionName: Crud::PAGE_INDEX),
                MenuItem::linkToCrud(label: 'Ajouter un produit', icon: 'fas fa-plus', entityFqcn: Product::class)
                    ->setAction(actionName: Crud::PAGE_NEW),
            ])
        ;

        // Commandes;
        $totalOrder = $this->orderRepository->count([]);
        yield MenuItem::section(label: 'Commandes', icon: 'fas fa-shopping-cart')
            ->setBadge(content: $totalOrder, style: 'dark')
        ;

        yield MenuItem::subMenu(label: 'Action', icon: 'fas fa-bars')->setSubItems(subItems: [
            MenuItem::linkToRoute(
                label: 'Voir les commandes',
                icon: 'fas fa-eye',
                routeName: 'app_admin_orders'
            ),
        ]);

        // Séparateur et déconnexion
        yield MenuItem::section();
        yield MenuItem::linkToLogout(label: 'Déconnexion', icon: 'fas fa-sign-out-alt text-white ms-1')
            ->setCssClass(cssClass: 'btn btn-danger text-white')
        ;
    }
}
