<?php

declare(strict_types=1);

namespace App\Tests\Controller\General;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FooterControllerTest extends WebTestCase
{
    /**
     * Tester que le footer est bien présent sur toutes les pages
     * @test
     */
    public function check_that_the_footer_is_present_on_the_homepage(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        self::assertSelectorExists(selector: 'footer');
        self::assertSelectorExists(selector: 'footer#footer');
    }
    /**
     * Tester que le footer contient la carte du Président
     * @test
     */
    public function footer_contains_the_president_card(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        self::assertSelectorExists(selector: 'footer#footer div#card-president');
    }
    /**
     * Tester que le footer contient la carte du Trésorier
     * @test
     */
    public function footer_contains_the_tresorier_card(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        self::assertSelectorExists(selector: 'footer#footer div#card-tresorier');
    }
    /**
     * Tester que le footer contient la carte du Trésorier
     * @test
     */
    public function footer_contains_the_secretaire_card(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        self::assertSelectorExists(selector: 'footer#footer div#card-secretaire');
    }
    /**
     * Tester que le footer contient la carte du Webmaster
     * @test
     */
    public function footer_contains_the_webmaster_card(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        self::assertSelectorExists(selector: 'footer#footer div#card-webmaster');
    }
    /**
     * Tester que le footer contient la carte du Trésorier
     * @test
     */
    public function footer_contains_the_structure_for_the_school_address(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        // 1. Vérifier que le footer contient une div address
        self::assertSelectorExists(selector: 'footer#footer div#address');
        // 2. Vérifier que la div address contient un span address line
        self::assertSelectorExists(selector: '#address-line');
        // 3. Vérifier que la div address contient un span address line-
        self::assertSelectorExists(selector: 'footer#footer div#address span#address-line-1');
        // 5. Vérifier que la div address contient un numéro de téléphone
        self::assertSelectorExists(selector: '#telephone');
        // 7. Vérifier que j'ai une balise <a> avec id email-school
        self::assertSelectorExists(selector: '#email-school');
    }
    /**
     * Tester que l'adresse de l'école est correct'
     * @test
     */
    public function footer_contains_the_correct_school_address(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        // 4. Vérifier que le span contient l'adresse exacte de l'école
        self::assertSelectorTextSame(selector: '#address-line-1', text: '8 Rue Pasteur, 59131 Rousies');
    }
    /**
     * Tester que le footer contient le bon numéro de téléphone
     * @test
     */
    public function footer_contains_the_correct_school_telephone_number(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        // 6. Vérifier que le numéro de téléphone est le bon
        self::assertSelectorTextSame(selector: '#telephone', text: '03 27 64 82 85');
    }
    /**
     * Tester que le footer contient la bonne adresse email ainsi que la bonne adresse dans l'attribut mailto
     * @test
     */
    public function footer_contains_the_correct_school_email_and_correct_mailto(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        // 8. Vérifier que l'adresse email est la bonne
        self::assertSelectorTextSame(selector: '#email-school', text: 'contact@pasteur.fr');
        // 9. Vérifier que le href contient "ce.0592114C@ac-lille.fr"
        self::assertStringContainsString(
            needle: 'mailto:ce.0592114C@ac-lille.fr',
            haystack: $client->getResponse()->getContent()
        );
    }
}
