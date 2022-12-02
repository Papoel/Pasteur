<?php

namespace App\Controller\Contact;

use App\Entity\Contact\Contact;
use App\Form\ContactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
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

            // Email
            $fullname = $contact->getFullName();
            $subject = $contact->getSubject();

            $email = (new TemplatedEmail());
            $email->from($contact->getEmail());
            $email->to('admin@aperp.fr');
            $email->bcc('pascal.briffard@gmail.com');
            if ($subject) {
                $email->subject($subject);
            } else {
                $email->subject('Message de ' . $fullname);
            }
            $email->htmlTemplate(template: 'emails/contact.html.twig');
            $email->context(context: ['contact' => $contact]);

            $mailer->send($email);

            $this->addFlash(type: 'success', message: 'Merci 🙏  ' .$contact->getFullname(). ', votre message a bien été envoyé.');

            return $this->redirectToRoute(route: 'app_contact');
        }

        return $this->render(view: 'contact/index.html.twig', parameters: [
            'form' => $form->createView(),
        ]);
    }
}
