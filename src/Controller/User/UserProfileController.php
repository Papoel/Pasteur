<?php

namespace App\Controller\User;

use App\Entity\User\User;
use App\Repository\Event\RegistrationEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    public function index(RegistrationEventRepository $registrationEventRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $registrations = $registrationEventRepository->findBy(['email' => $user->getEmail()]);

        // Get all data from user for profile page RGPD rules

        return $this->render(view: 'User/profile.html.twig', parameters: [
            'user' => $user,
            'registrations' => $registrations,
        ]);
    }
}
