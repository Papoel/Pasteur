<?php

namespace App\Controller\Admin;

use App\Entity\Contact\Contact;
use App\Entity\Event\Event;
use App\Entity\Event\Registration;
use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render(view: 'admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle(title: 'APE Rousies Pasteur - Administration')
            ->renderContentMaximized()
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard(label: 'Dashboard', icon: 'fa fa-home');
        yield MenuItem::linkToCrud(label: 'Utilisateurs', icon: 'fa-solid fa-user', entityFqcn: User::class);
        yield MenuItem::linkToCrud(label: 'Événements', icon: 'fas fa-jedi', entityFqcn: Event::class);
        yield MenuItem::linkToCrud(label: 'Inscriptions', icon: 'fas fa-calendar-check', entityFqcn: Registration::class);
        yield MenuItem::linkToCrud(label: 'Contact', icon: 'fas fa-envelope', entityFqcn: Contact::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
