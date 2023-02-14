<?php

declare(strict_types=1);

namespace App\Controller\Contact;

use App\Entity\Contact\Contact;
use App\Entity\User\User;
use App\Form\ContactFormType;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
    ): Response {
        $contact = new Contact();
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser) {
            $userName = $userRepository->find($currentUser)->getFullName();
            // Renseigné les informations de l'utilisateur connecté dans le formulaire
            $contact->setFullName($userName);
        }


        $form = $this->createForm(type: ContactFormType::class, data: $contact);
        $form->handleRequest(request: $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $contact->setIsReplied(false);

            if ($this->getUser() && $contact->getFullName() !== $userName) {
                $contact->setFullName($userName);
            }

            $entityManager->persist($contact);
            $entityManager->flush();

            // Envoyer un Email grâce au service MailService
            if (null === $contact->getSubject()) {
                $contact->setSubject(subject: 'Aperp - Nouveau message sans sujet');
            }

            $this->addFlash(type: 'success', message: 'Merci 🙏  '
                . $contact->getFullname() .
                ', votre message a bien été envoyé.');

            return $this->redirectToRoute(route: 'app_home');
        }

        return $this->render(view: 'contact/index.html.twig', parameters: [
            'form' => $form->createView(),
        ]);
    }
}
