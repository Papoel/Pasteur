<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Events;

use App\Repository\Event\EventRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EventRegistrationControllerTest extends WebTestCase
{
    /** @test */
    public function successfully_help_registration_page(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'event_help_registration_create', parameters: ['slug' => 'chasse-a-l-oeuf'])
        );

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK, message: 'La status code est incorrect.');
    }

    /** @test */
    public function admin_can_register_if_help_is_require(): void
    {
          /** 1. Je dois connecter un Admin */
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');

        $crawler = $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_login'));

        $form = $crawler->filter(selector: 'form[name=login]')->form([
            'email' => 'pascal.briffard@aperp.fr',
            'password' => 'Password1234!',
        ]);

        /**
         * 3. This is the third stage of my workflow
         */

        $client->submit($form);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FOUND);
        $client->followRedirect();

        /**
         * 2. This is the second stage of my workflow
         */


        $client->request(method: 'GET', uri: $urlGenerator->generate(name: 'app_events'));
        $crawler = $client->getCrawler();
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);

        $eventRepository = $client->getContainer()->get(id: EventRepository::class);
        $eventPublished = $eventRepository->findOneBy(criteria: ['published' => true]);
        $eventId = $eventPublished->getId();
        /** 3. Je dois cliquer sur 'Lire Plus' #link-to-details-$eventId */
        $client->click(link: $crawler->filter(selector: '#link-to-details-' .$eventId)->link());
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        /** Vérifier que la balise #alert-need-help est présente sur la page */
        self::assertSelectorExists(selector: '#alert-need-help', message: 'L\'appel au soutient n\est pas visible');
        $crawler = $client->getCrawler();
        $linkRegistration = $crawler->filter(selector: '#registration-help-' .$eventId)->attr(attribute: 'href');
        /** Récupérer le slug et vérifier que le lien mène à la bonne route */
        $eventSlug = $eventPublished->getSlug();
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertSame(
            expected: '/evenement/'. $eventSlug .'/inscription-aide/creation',
            actual: $linkRegistration,
            message: "Le href de la balise <a id=\"registration-help-id\">Remplir le formulaire de soutien</a> ne renvoie pas vers la bonne page"
        );
        /** Vérification du click */
        $client->click(link: $crawler->filter(selector: '#registration-help-' .$eventId)->link());
        //dd($client->getRequest()->getRequestUri());
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertRouteSame(
            expectedRoute: 'event_help_registration_create',
            message: "Le clique sur le lien 'Remplir le formulaire de soutien' ne renvoie pas vers le formulaire"
        );
       /** Vérifier le titre de la page Web */
        self::assertSelectorTextContains(
            selector: 'head > title',
            text: 'APERP | Inscription #' . $eventId,
            message: 'Le titre de la page n\'est pas correct'
        );
        /** Suivre le lien */
        $crawler =$client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator
                ->generate(name: 'event_help_registration_create', parameters: ['slug' => $eventSlug])
        );
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
    }
}
