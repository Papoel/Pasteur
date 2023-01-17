<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Events;

use App\Repository\Event\EventRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventsControllerTest extends WebTestCase
{
    /** Tester si la page événement s'affiche */
    public function testSuccessfullyEventsPage(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');

        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_events'));

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK, message: 'La page ne s\'affiche pas.');
    }
    /** Tester que le Block Title est implémenté */
    public function testIfTheTitleIsNotTheSimplyTitleFromBase(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_events'));

        $crawler = $client->getCrawler();
        $title = $crawler->filter(selector: 'title')->text();
        $titleFromBase = 'APERP |';

        self::assertNotSame(
            expected: $titleFromBase,
            actual: $title,
            message: 'Le titre de la page est incorrect'
        );
    }
    /** Tester si le Header (+ navbar) sont présent */
    public function testPageHasHeader(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');

        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_events'));

        // S'assurer que le "header" existe sur la page
        self::assertSelectorExists(selector: 'header', message: 'Le header n\'existe pas sur la page d\'accueil');
        self::assertSelectorExists(selector: '#menu', message: 'La menu navbar n\'existe pas sur la page d\'accueil');
    }
    /** Tester si le Footer est présent */
    public function testPageHasFooter(): void
    {
        // Créer un client et faire une requête "GET" sur la page d'accueil
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');

        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_events'));

        // S'assurer que le "footer" existe sur la page
        self::assertSelectorExists(selector: 'footer');
    }
    /** Tester si ma div Hero et son contenu statique est présent */
    public function testHeroDivIsHereAndStaticContentIsCorrect(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');

        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_events'));

        self::assertSelectorExists(selector: '#hero', message: 'La div Hero n\'existe pas sur la page événements');
        self::assertSelectorExists(selector: '#hero h1', message: 'La balise H1 n\'existe pas sur la page événements');

        $crawler = $client->getCrawler();
        $mainTitle = $crawler->filter(selector: '#main-title')->text();
        $textWaiting = 'Découvrez nos événements à venir.';

        self::assertSame(
            expected: $textWaiting,
            actual: $mainTitle,
            message: 'Le texte dans #main-title est incorrect'
        );
    }
    /** Tester si j'ai bien une carte événement affiché */
    public function testIfAllEventsCardPublishedAreDisplaying(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_events'));
        /** @var EventRepository $eventRepository */
        $eventRepository = $client->getContainer()->get(id: EventRepository::class);

        $countEvents = $eventRepository->countPublishedEvents();
        // Récupérer tous les id des événements publiés
        $events = $eventRepository->findAllPublishedEvents();
        // Mettre dans un tableau tous les id des événements publiés
        $eventsId = [];
        foreach ($events as $event) {
            $eventsId[] = $event->getId();
        }

        // Tester si j'ai bien le nombre de cartes attendues
        self::assertCount(
            expectedCount: $countEvents,
            haystack: $eventsId,
            message: 'Le nombre de cartes événements affichées est incorrect'
        );

        // Tester si j'ai bien les cartes événements avec id #card-event-$eventId[]
        foreach ($eventsId as $eventId) {
            self::assertSelectorExists(
                selector: '#card-event-'.$eventId,
                message: 'La carte événement n\'existe pas');
        }

        /*self::assertCount(
            expectedCount: $countEvents,
            haystack: $client->getCrawler()->filter(selector: '.card-event'),
            message: 'Il n\'y a pas le bon nombre de cartes événements'
        );*/

        /*self::assertSelectorExists(
            selector: '#card-events',
            message: "<div id=\"card-events\"> n'existe pas sur la page événements"
        );*/
    }
    /** Passer la carte au status "published => false"  pour tester si la carte "coming soon" s'affiche correctement */
    public function testIfThereAreNoneEventsPublishedTheComingSoonCardIsDisplay(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');

        /** @var EventRepository $eventRepository */
        $eventRepository = $client
            ->getContainer()
            ->get(id: EventRepository::class);

        $events = $eventRepository->findBy(criteria: ['published' => true]);

        foreach ($events as $event) {
            $event->setPublished(published: false);
        }

        $manager = $client->getContainer()->get(id: 'doctrine.orm.entity_manager');
        $manager->persist($event);
        $manager->flush();

        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_events'));

        self::assertCount(
            expectedCount: 1,
            haystack: $client->getCrawler()->filter(selector: '#card-coming-soon'),
            message: "La <div id=\"card-coming-soon\"> n'existe pas sur la page événements"
        );

        self::assertSelectorExists(
            selector: '#card-coming-soon',
            message: "<div id=\"card-coming-soon\"> n'existe pas sur la page événements"
        );
    }
    /** Tester si COMPLET s'affiche sur la carte dans le cas ou capacity == 0 */
    public function testIfCapacityIsZeroTheMentionOnTheCardIsComplet(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');

        /** @var EventRepository $eventRepository */
        $eventRepository = $client
            ->getContainer()
            ->get(id: EventRepository::class);

        $events = $eventRepository->findBy(criteria: ['published' => true]);
        foreach ($events as $event) {
            $event->setCapacity(capacity: 0);
        }

        $manager = $client->getContainer()->get(id: 'doctrine.orm.entity_manager');
        $manager->persist($event);
        $manager->flush();

        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_events'));

        $eventsId = [];
        foreach ($events as $event) {
            $eventsId[] = $event->getId();
        }

        $countEvents = $eventRepository->countEventsWithCapacityZero();

        // Count total of card-event-{id} with capacity == 0 on the page
        self::assertCount(
            expectedCount: $countEvents,
            haystack: $client->getCrawler()->filter(selector: '.card-event'),
            message: 'Il n\'y a pas le bon nombre de cartes événements avec la mention COMPLET'
        );

        // tester les cartes #event-full-$eventId
        foreach ($eventsId as $eventId) {
            self::assertSelectorExists(
                selector: '#event-full-'. $eventId,
                message: 'La mention "COMPLET" n\'est pas affichée sur la carte événement'
            );
        }
    }
    /** Tester si span id="event-capacity" a ce comportement "{{ event.capacity < 10 ? 'text-red-800 bg-red-100' : 'text-indigo-800 bg-indigo-100'}} */
    public function testIfTheEventCapacityIsUnder10ClassesAreAdded(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');

        /** @var EventRepository $eventRepository */
        $eventRepository = $client
            ->getContainer()
            ->get(id: EventRepository::class);

        /** Récupérer l'event publié et modifier sa capacité */
        $event = $eventRepository->findOneBy(criteria: ['published' => true]);
        $event->setCapacity(capacity: 5);
        $eventId = $event->getId();
        $manager = $client->getContainer()->get(id: 'doctrine.orm.entity_manager');
        $manager->persist($event);
        $manager->flush();

        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_events'));
        $crawler = $client->getCrawler();

        /** Récupérer dans le DOM la valeur de la span #event-capacity */
        $eventCapacity = $crawler->filter(selector: '#event-capacity-' .$eventId)->text();
        $capacityWaiting = '5';

        /** Récupérer toutes les classes de la span #event-capacity */
        $classEventCapacity = $crawler->filter(selector: '#event-capacity-' .$eventId)->attr(attribute: 'class');

        /** Vérifier que la span #event-capacity contient bien 'text-red-800 bg-red-100' */
        self::assertStringContainsString(
            needle: 'text-red-800 bg-red-100',
            haystack: $classEventCapacity,
            message: "Les classes \"text-red-800 bg-red-100\" n'existent pas sur le span id=\"event-capacity\""
        );

        /** Vérifier que la span #event-capacity contient bien la valeur 5 */
        self::assertSame(
            expected: $capacityWaiting,
            actual: $eventCapacity,
            message: "La capacité de l'événement n'est pas correcte"
        );

        /** Vérifier que la span #event-capacity est présente */
        self::assertSelectorExists(
            selector: '#event-capacity-' .$eventId,
            message: "<span id=\"event-capacity\"> n'existe pas sur la page événements"
        );
    }
    /** Tester si le bouton renvoie bien sur la bonne carte événement */
    public function testHrefLinkAndGoodRedirectToEventsPage(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_events'));

        /** @var EventRepository $eventRepository */
        $eventRepository = $client->getContainer()->get(id: EventRepository::class);
        $eventSlug = $eventRepository->findOneBy(criteria: ['published' => true])->getSlug();
        $eventId = $eventRepository->findOneBy(criteria: ['published' => true])->getId();

        $crawler = $client->getCrawler();
        $linkEvents = $crawler->filter(selector: '#link-to-details-' .$eventId)->attr(attribute: 'href');

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        /** Vérifier que le lien de Lire plus renvoie ver la bonne url */
        self::assertStringContainsString(
            needle: $eventSlug,
            haystack: $linkEvents,
            message: "Le lien <a id=\"link-to-details-{id}\">Lire Plus</a> ne renvoie pas vers la bonne page"
        );
        /** Vérifier qu'au' click je suis bien envoyé vers la bonne page */
        $client->click(link: $crawler->selectLink(value: 'Lire Plus')->link());
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertRouteSame(
            expectedRoute: 'app_event_show',
            parameters: ['slug' => $eventSlug],
            message: "Le clique sur le lien 'Lire plus' ne renvoie pas vers la bonne page"
        );
    }
    /** Tester si le prix est de 0€ j'ai bien GRATUIT avec le style attendu */
    public function testIfThePriceIsFree(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');

        /** @var EventRepository $eventRepository */
        $eventRepository = $client->getContainer()->get(id: EventRepository::class);
        $event = $eventRepository->findOneBy(criteria: ['published' => true]);
        $event->setPrice(price: 0);
        $manager = $client->getContainer()->get(id: 'doctrine.orm.entity_manager');
        $manager->persist($event);
        $manager->flush();
        $eventId = $event->getId();

        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_events'));
        $crawler = $client->getCrawler();

        /** Récupérer dans le DOM la valeur de la span #event-price-id */
        $eventPrice = $crawler->filter(selector: '#event-price-' .$eventId)->text();
        $priceFree = 'Gratuit';

        /** Récupérer toutes les classes de la span .event-price */
        $classEventPrice = $crawler->filter(selector: '#event-price-' .$eventId)->attr(attribute: 'class');
        /** Vérifier que la span #event-price contient bien 'text-green-800 bg-green-100' */
        self::assertStringContainsString(
            needle: 'bg-green-100 text-green-800 uppercase',
            haystack: $classEventPrice,
            message: "Les classes \"text-green-800 bg-green-100\" n'existent pas sur le span \"event-price\""
        );

        /** Vérifier que la span .event-price contient bien la valeur GRATUIT */
        self::assertSame(
            expected: $priceFree,
            actual: $eventPrice,
            message: "Le prix de l'événement n'est pas correcte"
        );

        /** Vérifier que la span #event-price est présente */
        self::assertSelectorExists(
            selector: '#event-price-' .$eventId,
            message: "<span id=\"event-price\"> n'existe pas sur la page événements"
        );
    }
    /** Tester la Route 'app_event_show' */
    public function testIfTheEventShowPageIsDisplay(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');

        /** @var EventRepository $eventRepository */
        $eventRepository = $client
            ->getContainer()
            ->get(id: EventRepository::class);

        $event = $eventRepository->findOneBy(criteria: ['published' => true]);

        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(
                name: 'app_event_show',
                parameters: ['slug' => $event->getSlug()]
            )
        );

        self::assertResponseIsSuccessful();
    }
}
