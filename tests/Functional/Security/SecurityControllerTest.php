<?php

declare(strict_types=1);

namespace App\Tests\Functional\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function testDisplayLogin(): void
    {
        $client = static::createClient();
        $client->request(method: 'GET', uri: '/connexion');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertSelectorNotExists(selector: '#alert');
    }

    public function testCorrectTitleDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request(method: 'GET', uri: '/connexion');

        $html = $crawler->html();
        self::assertStringContainsString(needle: 'Formulaire de connexion', haystack: $html);
        self::assertStringContainsString(needle: 'Connexion réservée aux membres de l\'APERP.', haystack: $html);
    }

    public function testLoginWithBadCredentials(): void
    {
        $client = static::createClient();
        $crawler = $client->request(method: 'GET', uri: '/connexion');
        $form = $crawler->selectButton(value: 'Connexion')->form([
            'email' => 'pascal.briffard@aperp.fr',
            'password' => 'badPassword',
            ]);
        $client->submit($form);
        self::assertResponseRedirects(expectedLocation: '/connexion');
        $client->followRedirect();
        self::assertSelectorExists(selector: '#alert');
    }

    /*public function testSuccessfullyLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request(method: 'GET', uri: '/connexion');
        $form = $crawler->selectButton(value: 'Connexion')->form([
            'email' => 'pascal.briffard@aperp.fr',
            'password' => 'Password1234!',
            ]);
        $client->submit($form);
        self::assertResponseRedirects(expectedLocation: '/');
    }*/
}
