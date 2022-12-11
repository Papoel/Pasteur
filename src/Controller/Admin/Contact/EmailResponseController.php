<?php

declare(strict_types=1);

namespace App\Controller\Admin\Contact;

use App\Form\ResponseEmailFormType;
use App\Repository\ContactRepository;
use App\Services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailResponseController extends AbstractController
{
    #[Route('/admin/response-message', name: 'app_response_message')]
    public function index(
        Request                $request,
        ContactRepository      $contactRepository,
        EntityManagerInterface $entityManager,
        MailService            $mailService,
    ): Response {

        // Récupérer l'id du message à répondre
        $queryString = $request->getQueryString('entityId');
        // Récupérer uniquement l'id'
        parse_str(string: $queryString, result: $params);
        // Transformer la valeur en entier
        $id = (int)$params['entityId'];

        // Récupérer l'entité du message à répondre
        $contact = $contactRepository->find(id: $id);

        // Création du formulaire de réponse
        $form = $this->createForm(type: ResponseEmailFormType::class, data: $contact);
        // Récupération des données du formulaire
        $form->handleRequest(request: $request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le message de réponse
            $response = $form->getData();
            // Mettre isReplied à true
            $contact->setIsReplied(isReplied: true);
            // Mettre la réponse dans le message
            $contact->setResponse(response: $response->getResponse());
            // Mettre la date de réponse
            $contact->setReplyAt(replyAt: new \DateTimeImmutable());

            $entityManager->persist($contact);
            $entityManager->flush();


            // TODO: Pourquoi l'email ne s'envoie pas ?
            $mailService->sendEmail(
                from: 'aperousiespasteur@gmail.com',
                subject: 'Aperp - Votre message a été traité',
                htmlTemplate: 'emails/response.html.twig',
                context: [
                    'contact' => $contact,
                ],
            );

            $this->addFlash(
                type: 'success',
                message: 'Votre réponse a ' . $contact->getFullName() . ' a bien été envoyée.'
            );

            return $this->redirectToRoute(route: 'admin');
        }

        return $this->render(view: 'admin/contact/response.html.twig', parameters: [
            'form' => $form->createView(),
            'contact' => $contact,
        ]);
    }
}
