<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\General;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomePageControllerTest extends WebTestCase
{
    /**
     * Tester que la page d'accueil s'affiche correctement
     * @test
     */
    public function the_page_homepage_is_correctly_display(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK, message: 'La status code est incorrect.');
    }
    /**
     * Tester que le Block Title est implémenté
     * @test
     */
    public function the_title_is_not_inherited_from_the_base_template(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $crawler = $client->getCrawler();
        $title = $crawler->filter(selector: 'title')->text();
        $titleFromBase = 'APERP |';

        self::assertNotSame(
            expected: $titleFromBase,
            actual: $title,
            message: 'Le titre de la page est incorrect'
        );
    }
    /**
     * Tester si le Header (+ navbar) sont présent
     * @test
     */
    public function page_contains_the_header(): void
    {
        // Créer un client et faire une requête "GET" sur la page d'accueil
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        // S'assurer que le "header" existe sur la page
        self::assertSelectorExists(selector: 'header', message: 'Le header n\'existe pas sur la page d\'accueil');
        self::assertSelectorExists(selector: '#menu', message: 'La menu navbar n\'existe pas sur la page d\'accueil');
    }
    /** Tester si le Footer est présent
     * @test
     */
    public function page_contains_the_footer(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        // S'assurer que le "footer" existe sur la page
        self::assertSelectorExists(selector: 'footer');
    }
    /**
     * Tester le contenu statique de la page d'accueil
     * @test
     */
    public function check_if_the_structure_of_the_page_is_correct(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
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
    /** Tester si le titre h1 de ma page est correct
     * @test
     */
    public function the_title_on_the_page_is_correct(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        self::assertSelectorExists(selector: 'h1', message: 'La balise H1 n\'existe pas sur la page événements');
        self::assertSelectorTextSame(
            selector: 'h1',
            text: 'Association des Parents d\'Élèves',
            message: 'Le titre de la page est incorrect'
        );
    }
    /**
     * Tester que le sous titre est bien le nom de l'école
     * @test
     */
    public function subtitle_is_the_name_of_the_school(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        self::assertSelectorTextSame(
            selector: '#name-school',
            text: 'École primaire Pasteur - Rousies'
        );
    }
    /**
     * Tester Que j'ai bien 3 spans pour la présentation de l'APE
     * @test
     */
    public function count_if_3_spans_are_displayed(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        // Obtenir le crawler pour la réponse
        $crawler = $client->getCrawler();

        // S'assurer que j'ai bien 3 spans dans l'élément blockquote
        $spans = $crawler->filter(selector: 'div#presentation figure blockquote span');
        self::assertCount(expectedCount: 3, haystack: $spans);
    }
    /**
     * Tester le span 1 contient bien le texte attendu
     * @test
     */
    public function the_text_in_presentation_line_1_is_correct(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $crawler = $client->getCrawler();
        $presentationLine1 = $crawler->filter(selector: '#presentation-line-1')->text();
        $textWaiting = "Nous sommes une association de parents d'élèves qui travaillent en étroite collaboration avec l'école pour améliorer les conditions d'apprentissage de nos enfants.";

        self::assertSame(
            expected: $textWaiting,
            actual: $presentationLine1,
            message: 'Le texte dans #presentation-line-1 est incorrect'
        );
    }
    /**
     * Tester le span 2 contient bien le texte attendu
     * @test
     */
    public function the_text_in_presentation_line_2_is_correct(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

            $crawler = $client->getCrawler();
            $presentationLine2 = $crawler->filter(selector: '#presentation-line-2')->text();
            $textWaiting = "Nous organisons régulièrement des réunions pour discuter de sujets importants et préparer des événements pour les élèves et les parents.";

            self::assertSame(
                expected: $textWaiting,
                actual: $presentationLine2,
                message: 'Le texte dans #presentation-line-2 est incorrect'
            );
    }
    /**
     * Tester le span 3 contient bien le texte attendu
     * @test
     */
    public function the_text_in_presentation_line_3_is_correct(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

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
    /**
     * Tester que la page d'accueil contient l'affichage des messages flash
     * @test
     */
    public function the_page_can_display_flash_message(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $crawler = $client->getCrawler();
        $elements = $crawler->filter(selector: 'div[role="alert"]');
        self::assertGreaterThan(
            expected: 0,
            actual: count($elements),
            message: 'La page ne contient aucun élément correspondant au sélecteur: "div[role="alert"]"'
        );
    }
    /**
     * Tester si le bouton renvoie bien vers la page Événement
     * @test
     */
    public function links_in_the_button_refer_to_events_page(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

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
    /**
     * Tester si le lien "Contact" existe et s'il renvoie à la bonne page "app_contact"
     * @test
     */
    public function the_link_to_contact_refer_to_contact_page(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

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
