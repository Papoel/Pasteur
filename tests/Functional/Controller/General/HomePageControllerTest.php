<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\General;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class HomePageControllerTest extends WebTestCase
{
    /** Tester si la page d'accueil retourne un status Code 200*/
    public function testGetSuccessfullyHomePage(): void
    {
        // Créer un client et faire une requête "GET" sur la page d'accueil
        $client = static::createClient();
        $client->request(method: 'GET', uri: '/');

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
        $client->request(method: 'GET', uri: '/');

        // Obtenir le crawler pour la réponse
        $crawler = $client->getCrawler();

        // S'assurer que l'élément de figure existe
        self::assertSelectorExists(selector: 'figure');

        // S'assurer que l'élément svg existe
        self::assertSelectorExists(selector: 'svg');

        // Vérifier que l'élément h1 existe et contient le texte attendu.
        self::assertSelectorTextContains(selector: 'h1', text: 'Association des Parents d\'Élèves');
        // Vérifier que l'élément p existe et contient le texte attendu.
        self::assertSelectorTextContains(selector: 'p', text: 'École primaire Pasteur - Rousies');

        // Sélectionnez les éléments span dans l'élément blockquote.
        $spans = $crawler->filter(selector: 'div#presentation figure blockquote')->children();

        $spans->each(closure: function (Crawler $node) {
            $text = $node->text();
            if (
                str_contains($text, "Nous sommes une association de parents d'élèves qui travaillons en étroite 
            collaboration avec l'école pour améliorer les conditions d'apprentissage de nos enfants.")
            ) {
                self::assertStringContainsString(
                    needle: "Nous sommes une association de parents d'élèves qui travaillons en étroite collaboration 
                    avec l'école pour améliorer les conditions d'apprentissage de nos enfants.",
                    haystack: $text
                );
            } elseif (
                str_contains($text, "Nous organisons régulièrement des réunions pour discuter de sujets importants et 
                préparer des événements pour les élèves et les parents.")
            ) {
                $this->assertStringContainsString(
                    needle: "Nous organisons régulièrement des réunions pour discuter de sujets importants et préparer 
                    des événements pour les élèves et les parents.",
                    haystack: $text
                );
            } elseif (
                str_contains($text, "Si vous souhaitez en savoir plus sur notre association ou si vous souhaitez 
                vous impliquer, n'hésitez pas à nous contacter via la page de contact ou à vous rendre à une de nos 
                réunions ouvertes à tous.")
            ) {
                $this->assertStringContainsString(
                    needle: "Si vous souhaitez en savoir plus sur notre association ou si vous souhaitez 
                    vous impliquer, n'hésitez pas à nous contacter via la page de contact ou à vous rendre à une de nos
                    réunions ouvertes à tous.",
                    haystack: $text
                );
            }
        });
    }

    /** Tester le contenu statique de la page d'accueil : 3 spans */
    public function testCountPageHas3Spans(): void
    {
        $client = static::createClient();
        $client->request(method: 'GET', uri: '/');

        // Obtenir le crawler pour la réponse
        $crawler = $client->getCrawler();

        // S'assurer que j'ai bien 3 spans dans l'élément blockquote
        $spans = $crawler->filter(selector: 'div#presentation figure blockquote span');
        self::assertCount(expectedCount: 3, haystack: $spans);
    }

    /** Tester si la page d'accueil contient l'affichage des messages flash */
    public function testContainsFlashMessageDiv(): void
    {
        $client = static::createClient();
        $client->request(method: 'GET', uri: '/');

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
        $client->request(method: 'GET', uri: '/');
        $crawler = $client->getCrawler();

        // Récupère le lien contenu dans le bouton
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
        $client->request(method: 'GET', uri: '/');
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
