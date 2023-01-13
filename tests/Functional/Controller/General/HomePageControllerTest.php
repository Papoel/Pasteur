<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\General;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomePageControllerTest extends WebTestCase
{
    /** Tester si la page d'accueil retourne un status Code 200*/
    public function testGetSuccessfullyHomePage(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        self::assertEquals(expected: 200, actual: $client->getResponse()->getStatusCode());
    }

    /** Tester si le Header est sur la page d'accueil */
    public function testPageHasHeader(): void
    {
        // Créer un client et faire une requête "GET" sur la page d'accueil
        $client = static::createClient();
        $client->request(method: 'GET', uri: '/');

        // Obtenir le crawler pour la réponse
        $crawler = $client->getCrawler();

        // S'assurer que le "header" existe sur la page
        self::assertSelectorExists(selector: 'header');
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
        self::assertSelectorExists(selector: '#presentation', message: 'La balise \'<div id=\'presentation\'></div>\' doit être présente');
        self::assertSelectorExists(selector: '#presentation-line', message: 'La balise \'<p id=\'presentation-line\'></p>\' doit être présente');
        self::assertSelectorExists(selector: '#presentation-line-1', message: 'La balise \'<p id=\'presentation-line-1\'></p>\' doit être présente');
        self::assertSelectorExists(selector: '#presentation-line-2', message: 'La balise \'<p id=\'presentation-line-2\'></p>\' doit être présente');
        self::assertSelectorExists(selector: '#presentation-line-3', message: 'La balise \'<p id=\'presentation-line-3\'></p>\' doit être présente');

    }

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

    /** Tester si la page d'accueil contient l'affichage des messages flash */
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

    /** Tester si le bouton renvoie bien ver la page Événement */
    public function testButtonRedirectToEvents(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        $crawler = $client->getCrawler();

        $link = $crawler->selectLink(value: 'Nos prochains événements')->link();
        // Clique sur le lien
        $client->click($link);

        // Vérifie que la réponse est un succès (code de réponse 2xx)
        self::assertResponseIsSuccessful();
        // Vérifie que le bouton renvoie bien vers la route 'app_events'
        self::assertRouteSame(
            expectedRoute: 'app_events',
            message: 'Le bouton de redirection vers les événements ne renvoie pas vers la bonne route',
        );
    }

    /** Tester si le lien "Contact" existe et s'il renvoie à la bonne page "app_contact"*/
    public function testRedirectContactLinks(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        $crawler = $client->getCrawler();

        // Récupère le lien contenu dans le bouton
        $link = $crawler->selectLink(value: 'contact')->link();
        // Clique sur le lien
        $client->click($link);

        // Vérifie que la réponse est un succès (code de réponse 2xx)
        self::assertResponseIsSuccessful();
        // Vérifie que le bouton renvoie bien vers la route 'app_events'
        self::assertRouteSame(
            expectedRoute: 'app_contact',
            message: 'Le bouton de redirection vers contact ne renvoie pas vers la bonne route',
        );
    }
}
