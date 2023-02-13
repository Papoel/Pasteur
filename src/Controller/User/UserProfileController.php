<?php

namespace App\Controller\User;

use App\Entity\Contact\Contact;
use App\Entity\User\User;
use App\Repository\Event\RegistrationEventRepository;
use App\Services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserProfileController extends AbstractController
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }
    #[Route('/{user}/profile', name: 'app_user_profile')]
    #[isGranted('ROLE_ADMIN')]
    public function index(RegistrationEventRepository $registrationEventRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $registrations = $registrationEventRepository->findBy(['email' => $user->getEmail()]);

        // Get all data from user for profile page RGPD rules

        return $this->render(view: 'user/profile.html.twig', parameters: [
            'user' => $user,
            'registrations' => $registrations,
        ]);
    }

    #[Route('/user/profile/delete', name: 'app_user_profile_delete')]
    #[isGranted('ROLE_ADMIN')]
    public function delete(MailService $mailService, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $contact->setFullName($currentUser->getFullName());
        $contact->setEmail($currentUser->getEmail());
        $contact->setSubject(subject: 'Demande de suppression de compte');
        $contact->setMessage($currentUser->getFullName() .' demande de suppression de son compte APERP');
        $contact->setIsReplied(isReplied: true);

        $entityManager->persist($contact);
        $entityManager->flush();

        $mailService->sendEmail(
            from: $contact->getEmail(),
            to: 'contact@aperp.info',
            subject: $contact->getSubject(),
            htmlTemplate: 'emails/delete_account.html',
            context: [
                'fullname' => $contact->getFullName(),
                'createdAt' => $contact->getCreatedAt(),
            ],
        );

        $this->addFlash(
            type: 'success',
            message: 'Votre demande de suppression de compte a bien été transmise à l\'administrateur du site.'
        );

        return $this->redirectToRoute(route: 'app_user_profile', parameters: [
            'user' => $currentUser,
        ]);

    }

    // CGU
    #[Route('/user/profile/cgu', name: 'app_cgu')]
    public function cgu(): Response
    {
        return $this->render(view: 'user/cgu.html.twig');
    }
}
