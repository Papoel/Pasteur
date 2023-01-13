<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\General;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FooterControllerTest extends WebTestCase
{
    public function testFooterIsDisplay(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        self::assertSelectorExists(selector: 'footer#footer');
    }

    public function testFooterContainCardPresident(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        self::assertSelectorExists(selector: 'footer#footer div#card-president');
    }

    public function testFooterContainCardTresorier(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        self::assertSelectorExists(selector: 'footer#footer div#card-tresorier');
    }

    public function testFooterContainCardSecretaire(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        self::assertSelectorExists(selector: 'footer#footer div#card-secretaire');
    }

    public function testFooterContainCardWebmaster(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        self::assertSelectorExists(selector: 'footer#footer div#card-webmaster');
    }

    public function testFooterContainCorrectStructureForAddress(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
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

    public function testFooterContainGoodAddress(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        // 4. Vérifier que le span contient l'adresse exacte de l'école
        self::assertSelectorTextSame(selector: '#address-line-1', text: '8 Rue Pasteur, 59131 Rousies');
    }

    public function testFooterContainGoodTelephone(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
        );

        // 6. Vérifier que le numéro de téléphone est le bon
        self::assertSelectorTextSame(selector: '#telephone', text: '03 27 64 82 85');
    }

    public function testFooterContainGoodEmailAndGoodText(): void
    {
        $client = static::createClient();
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');
        $client->request(
            method: Request::METHOD_GET,
            uri: $urlGenerator->generate(name: 'app_home')
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
