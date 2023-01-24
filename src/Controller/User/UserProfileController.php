<?php

namespace App\Controller\User;

use App\Entity\User\User;
use App\Repository\Event\RegistrationEventRepository;
use App\Services\MailService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserProfileController extends AbstractController
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }
    #[Route('/user/profile', name: 'app_user_profile')]
    #[isGranted('ROLE_ADMIN')]
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

    #[Route('/user/profile/delete', name: 'app_user_profile_delete')]
    #[isGranted('ROLE_ADMIN')]
    public function DeleteAccount(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $email = (new TemplatedEmail())
            ->from($user->getEmail())
            ->to(new Address(address: 'aperousiespasteur@gmail.com'))
            ->subject(subject: 'Demande de suppression de compte!')

            // path of the Twig template to render
            ->htmlTemplate(template: 'emails/delete_account.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'username' => $user->getFirstname() . ' ' . $user->getLastname(),
            ]);

            $this->mailer->send($email);

            $this->addFlash(
                type: 'success',
                message: 'Votre demande de suppression de compte a bien été transmise à l\'administrateur du site.'
            );

        return $this->redirectToRoute(route: 'app_user_profile');
    }
}
