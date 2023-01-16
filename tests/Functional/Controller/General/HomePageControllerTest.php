<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\General;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomePageControllerTest extends WebTestCase
{
    public function testGetSuccessfullyHomePage(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK, message: 'La status code est incorrect.');
    }
    /** Tester que le Block Title est implémenté */
    public function testIfTheTitleIsNotTheSimplyTitleFromBase(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_home'));

        $crawler = $client->getCrawler();
        $title = $crawler->filter(selector: 'title')->text();
        $titleFromBase = 'APERP |';

        self::assertNotSame(
            expected: $titleFromBase,
            actual: $title,
            message: 'Le titre de la page est incorrect'
        );
    }

    /** Tester si le Header et la navbar est sur la page d'accueil */
    public function testPageHasHeader(): void
    {
        // Créer un client et faire une requête "GET" sur la page d'accueil
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(method: 'GET', uri: $urlGenerator->generate(name: 'app_home'));

        // S'assurer que le "header" existe sur la page
        self::assertSelectorExists(selector: 'header', message: 'Le header n\'existe pas sur la page d\'accueil');
        self::assertSelectorExists(selector: '#menu', message: 'La menu navbar n\'existe pas sur la page d\'accueil');
    }

    /** Tester si le Footer est sur la page d'accueil */
    public function testPageHasFooter(): void
    {
        // Créer un client et faire une requête "GET" sur la page d'accueil
        $client = static::createClient();
        $client->request(method: 'GET', uri: '/');

        // S'assurer que le "footer" existe sur la page
        self::assertSelectorExists(selector: 'footer');
    }

    /** Tester le contenu statique de la page d'accueil */
    public function testPageContent(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        self::assertSelectorExists(selector: 'figure', message: 'La balise \'Figure\' doit être présent');
        self::assertSelectorExists(selector: 'svg', message: 'Le \'SVG\' doit être présent');
        self::assertSelectorExists(
            selector: '#presentation',
            message: 'La balise \'<div id=\'presentation\'></div>\' doit être présente'
        );
        self::assertSelectorExists(
            selector: '#presentation-line',
            message: 'La balise \'<p id=\'presentation-line\'></p>\' doit être présente'
        );
        self::assertSelectorExists(
            selector: '#presentation-line-1',
            message: 'La balise \'<p id=\'presentation-line-1\'></p>\' doit être présente'
        );
        self::assertSelectorExists(
            selector: '#presentation-line-2',
            message: 'La balise \'<p id=\'presentation-line-2\'></p>\' doit être présente'
        );
        self::assertSelectorExists(
            selector: '#presentation-line-3',
            message: 'La balise \'<p id=\'presentation-line-3\'></p>\' doit être présente'
        );
    }
    /** Tester le titre de la page d'accueil */
    public function testHomepageContainGoodTitle(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        // Vérifier que l'élément h1 existe et contient le texte attendu.
        self::assertSelectorTextSame(selector: 'h1', text: 'Association des Parents d\'Élèves');
    }
    /** Tester que le sous titre est bien le nom de l'école */
    public function testHomepageContainGoodSubTitleWithNameSchool(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        // Vérifier que l'élément h1 existe et contient le texte attendu.
        self::assertSelectorTextSame(
            selector: '#name-school',
            text: 'École primaire Pasteur - Rousies'
        );
    }
    /** Tester Que j'ai bien 3 spans pour la présentation de l'APE */
    public function testCountPageHas3Spans(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        // Obtenir le crawler pour la réponse
        $crawler = $client->getCrawler();

        // S'assurer que j'ai bien 3 spans dans l'élément blockquote
        $spans = $crawler->filter(selector: 'div#presentation figure blockquote span');
        self::assertCount(expectedCount: 3, haystack: $spans);
    }
    /** Tester le span 1 contient bien le texte attendu */
    public function testHomepageContainGoodTextPresentationLine1(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_home'));

        $crawler = $client->getCrawler();
        $presentationLine1 = $crawler->filter(selector: '#presentation-line-1')->text();
        $textWaiting = "Nous sommes une association de parents d'élèves qui travaillent en étroite collaboration avec l'école pour améliorer les conditions d'apprentissage de nos enfants.";

        self::assertSame(
            expected: $textWaiting,
            actual: $presentationLine1,
            message: 'Le texte dans #presentation-line-1 est incorrect'
        );
    }
    /** Tester le span 2 contient bien le texte attendu */
    public function testHomepageContainGoodTextPresentationLine2(): void
    {
            $client = static::createClient();

            $urlGenerator = $client->getContainer()->get(id: 'router');
            $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_home'));

            $crawler = $client->getCrawler();
            $presentationLine2 = $crawler->filter(selector: '#presentation-line-2')->text();
            $textWaiting = "Nous organisons régulièrement des réunions pour discuter de sujets importants et préparer des événements pour les élèves et les parents.";

            self::assertSame(
                expected: $textWaiting,
                actual: $presentationLine2,
                message: 'Le texte dans #presentation-line-2 est incorrect'
            );
    }
    /** Tester le span 3 contient bien le texte attendu */
    public function testHomepageContainGoodTextPresentationLine3(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_home'));

        $crawler = $client->getCrawler();
        $presentationLine3 = $crawler->filter(selector: '#presentation-line-3')->text();
        $textWaiting = "Si vous souhaitez en savoir plus sur notre association ou si vous souhaitez vous impliquer, n'hésitez pas à nous contacter via la page de contact ou à vous rendre à une de nos réunions ouvertes à tous.";

        // dd(["Texte HTML" => $presentationLine3, "_Variable_" => $textWaiting]);

        self::assertSame(
            expected: $textWaiting,
            actual: $presentationLine3,
            message: 'Le texte dans #presentation-line-3 est incorrect'
        );
    }

    /** Tester que la page d'accueil contient l'affichage des messages flash */
    public function testContainsFlashMessageDiv(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        $crawler = $client->getCrawler();
        $elements = $crawler->filter(selector: 'div[role="alert"]');
        self::assertGreaterThan(
            expected: 0,
            actual: count($elements),
            message: 'La page ne contient aucun élément correspondant au sélecteur: "div[role="alert"]"'
        );
    }
    /** Tester si le bouton renvoie bien vers la page Événement */
    public function testHrefLinkAndGoodRedirectToEventsPage(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_home'));

        $crawler = $client->getCrawler();
        $linkEvents = $crawler->filter(selector: '.link-to-events')->attr(attribute: 'href');

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertSame(
            expected: '/evenements',
            actual: $linkEvents,
            message: "Le href de la balise <a class=\"link-to-events\">Nos prochains événements</a> ne renvoie pas vers la bonne page"
        );

        // Cliquer sur le linkContact et s'assurer que la page est bien la page de contact
        $client->click(link: $crawler->filter(selector: '.link-to-events')->link());
        // dd($client->getRequest()->getRequestUri());
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertRouteSame(
            expectedRoute: 'app_events',
            message: "Le clique sur le lien 'Nos prochains événements' ne renvoie pas vers la page événements"
        );
    }
    /** Tester si le lien "Contact" existe et s'il renvoie à la bonne page "app_contact"*/
    public function testHrefLinkAndGoodRedirectToContactPage(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(method: Request::METHOD_GET, uri: $urlGenerator->generate(name: 'app_home'));

        $crawler = $client->getCrawler();
        $linkContact = $crawler->filter(selector: '.link-to-contact')->attr(attribute: 'href');

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertSame(
            expected: '/contact',
            actual: $linkContact,
            message: "Le href de la balise <a class=\"link-to-contact\">Contact</a> ne renvoie pas vers la bonne page"
        );

        // Cliquer sur le linkContact et s'assurer que la page est bien la page de contact
        $client->click(link: $crawler->filter(selector: '.link-to-contact')->link());
        // dd($client->getRequest()->getRequestUri());
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertRouteSame(
            expectedRoute: 'app_contact',
            message: "Le clique sur le lien Contact ne renvoie pas vers la page contact"
        );
    }
}
