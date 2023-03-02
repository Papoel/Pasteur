<?php

namespace App\Controller\User;

use App\Entity\Contact\Contact;
use App\Entity\User\User;
use App\Form\User\UserType;
use App\Repository\User\UserRepository;
use App\Services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    #[isGranted('ROLE_SUPER_ADMIN')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render(view: 'user/crud/index.html.twig', parameters: [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    #[isGranted('ROLE_SUPER_ADMIN')]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(type: UserType::class, data: $user, options: ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $userRepository->save(entity: $user, flush: true);

            return $this->redirectToRoute(route: 'app_user_index', parameters: [], status: Response::HTTP_SEE_OTHER);
        }

        return $this->render(view: 'user/crud/new.html.twig', parameters: [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render(view: 'user/crud/show.html.twig', parameters: [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(type: UserType::class, data: $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save(entity: $user, flush: true);

            return $this->redirectToRoute(route: 'app_user_show', parameters: ['id' => $user->getId()], status: Response::HTTP_SEE_OTHER);
        }

        return $this->render(view: 'user/crud/edit.html.twig', parameters: [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/asking-delete', name: 'app_user_delete_asking', methods: ['GET', 'POST'])]
    public function askingDelete(
        MailService $mailService,
        EntityManagerInterface $entityManager,
        User $user
    ): Response {
        $contact = new Contact();

        // Set in table User the field AskDeleteAccountAt to now
        $user->setAskDeleteAccountAt(new \DateTimeImmutable());

        $contact->setFullName($user->getFullName());
        $contact->setEmail($user->getEmail());
        $contact->setSubject(subject: 'Demande de suppression de compte');
        $contact->setMessage($user->getFullName() . ' demande de suppression de son compte APERP');
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

        return $this->redirectToRoute(route: 'app_user_show', parameters: [
            'user' => $user,
            'id' => $user->getId()
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid(id: 'delete' . $user->getId(), token: $request->request->get(key: '_token'))) {
            $userRepository->remove($user, flush: true);

            $this->addFlash(
                type: 'success',
                message: 'Le compte a bien été supprimé.'
            );
        }

        return $this->redirectToRoute(route: 'app_user_index', parameters: [], status: Response::HTTP_SEE_OTHER);
    }
}
