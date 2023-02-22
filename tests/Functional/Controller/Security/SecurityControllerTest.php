<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Security;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecurityControllerTest extends WebTestCase
{
    /**
     * Tester si la page de connexion s'affiche
     * @test
     */
    public function the_page_login_is_correctly_display(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_login', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );
        $crawler = $client->getCrawler();

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertSelectorNotExists(selector: '#alert');

        $html = $crawler->html();
        self::assertStringContainsString(needle: 'Formulaire de connexion', haystack: $html);
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
                ->generate(name: 'app_login', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
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
     * Se connecter avec des identifiants correct
     * @test
     */
    public function login_with_good_credentials(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_login', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );
        $crawler = $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_login', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $form = $crawler->filter(selector: 'form[name=login]')->form([
            'email' => 'papoel@aperp.fr',
            'password' => 'Password1234!',
        ]);

        $client->submit($form);

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FOUND);
        $client->followRedirect();

        self::assertRouteSame(expectedRoute: 'app_home');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
    }
    /**
     * Se connecter avec des identifiants correct
     * @test
     */
    public function login_with_bad_credentials(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_login', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );
        $crawler = $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_login', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $form = $crawler->filter(selector: 'form[name=login]')->form([
            'email' => 'papoel@aperp.fr',
            'password' => 'BadPassword1234!',
        ]);

        $client->submit($form);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame(expectedRoute: 'app_login');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);

        self::assertSelectorExists(selector: '#error-login');
    }
    /**
     * La déconnexion fonctionne et me redirige vers la page d'accueil
     * @test
     */
    public function logout_work_correctly_and_redirect_to_homepage(): void
    {
        $client = static::createClient();
        $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_login', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        /** @var UserRepository $userRepository */
        $userRepository = $client
            ->getContainer()
            ->get(id: UserRepository::class);

        /** @var User $user */
        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        $crawler = $client->request(
            method: Request::METHOD_GET,
            uri: $client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_logout', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame(expectedRoute: 'app_home');
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
    }
}
