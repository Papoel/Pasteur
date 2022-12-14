<?php

namespace App\Controller\Admin;

use App\Entity\Contact\Contact;
use App\Entity\Event\Event;
use App\Entity\User\User;
use App\Repository\Contact\ContactRepository;
use App\Repository\Event\EventRepository;
use App\Repository\User\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EventRepository $eventRepository,
        private readonly ContactRepository $contactRepository,
        private readonly UserRepository $user
    ) {
    }


    #[Route('/admin', name: 'admin')]
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

        $totalUsers = $this->userRepository->count([]);
        yield MenuItem::section(label: 'Utilisateurs', icon: 'fa fa-users')
            ->setBadge(content: $totalUsers)
        ;
        yield MenuItem::subMenu(label: 'Action', icon: 'fas fa-bars')->setSubItems(subItems: [
            MenuItem::linkToCrud(label: 'Voir les utilisateurs', icon: 'fas fa-eye', entityFqcn: User::class),
            MenuItem::linkToCrud(label: 'Ajouter un membre', icon: 'fas fa-plus', entityFqcn: User::class)
                ->setAction(actionName: Crud::PAGE_NEW),
        ]);

        $totalEvents = (string) $this->eventRepository->countNotPastEvents();
        yield MenuItem::section(label: 'Événements', icon: 'fas fa-jedi')
            ->setBadge($totalEvents, style: 'info')
        ;
        yield MenuItem::subMenu(label: 'Action', icon: 'fas fa-bars')->setSubItems(subItems: [
            MenuItem::linkToCrud(label: 'Voir les événements', icon: 'fas fa-eye', entityFqcn: Event::class),
            MenuItem::linkToCrud(label: 'Ajouter un événement', icon: 'fas fa-plus', entityFqcn: Event::class)
                ->setAction(actionName: Crud::PAGE_NEW),
        ]);

        /*yield MenuItem::section(label: 'Inscriptions', icon: 'fas fa-calendar-check');
        yield MenuItem::subMenu(label: 'Action', icon: 'fas fa-bars')->setSubItems(subItems: [
            MenuItem::linkToCrud(label: 'Voir les inscriptions', icon: 'fas fa-eye', entityFqcn: Registration::class),
            MenuItem::linkToCrud(label: 'Ajouter une inscription', icon: 'fas fa-plus', entityFqcn: Registration::class)
                ->setAction(actionName: Crud::PAGE_NEW),
        ]);*/

        $totalMessages = $this->contactRepository->count(['isReplied' => false]);
        yield MenuItem::section(label: 'Messages', icon: 'fas fa-envelope')
            ->setBadge($totalMessages, style: 'warning')
            ->setPermission(permission: 'ROLE_PRESIDENT')
        ;
        yield MenuItem::subMenu(label: 'Action', icon: 'fas fa-bars')->setSubItems(subItems: [
            MenuItem::linkToCrud(label: 'Voir tous les messages', icon: 'fas fa-eye', entityFqcn: Contact::class)
                ->setPermission(permission: 'ROLE_PRESIDENT'),
        ]);
    }
}
