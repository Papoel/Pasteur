<?php

namespace App\Controller\Contact;

use App\Entity\Contact\Contact;
use App\Form\ContactFormType;
use App\Services\MailService;
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
        MailService $mailService,
    ): Response {
        $contact = new Contact();

        // Renseigné les informations de l'utilisateur connecté dans le formulaire
        if ($this->getUser()) {
            $contact->setFullName($this->getUser()->getFullName());
            $contact->setEmail($this->getUser()->getEmail());
        }

        $form = $this->createForm(type: ContactFormType::class, data: $contact);

        $form->handleRequest(request: $request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $entityManager->persist($contact);
            $entityManager->flush();

            // Envoyer un Email grâce au service MailService
            if ($contact->getSubject() === null) {
                $contact->setSubject(subject: 'Aperp - Nouveau message sans sujet');
            }
            $mailService->sendEmail(
                from: $contact->getEmail(),
                subject: $contact->getSubject(),
                htmlTemplate: 'emails/contact.html.twig',
                context: [
                    'contact' => $contact,
                ],
            );

            $this->addFlash(type: 'success', message: 'Merci 🙏  ' .$contact->getFullname(). ', votre message a bien été envoyé.');

            return $this->redirectToRoute(route: 'app_contact');
        }

        return $this->render(view: 'contact/index.html.twig', parameters: [
            'form' => $form->createView(),
        ]);
    }
}
