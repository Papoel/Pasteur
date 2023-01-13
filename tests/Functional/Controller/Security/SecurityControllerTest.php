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
    public function testLoginPageDisplay(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');

        $crawler = $client
            ->request(
                method: Request::METHOD_GET,
                uri: $urlGenerator->generate(name: 'app_login')
            );

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertSelectorNotExists(selector: '#alert');

        $html = $crawler->html();
        self::assertStringContainsString(needle: 'Formulaire de connexion', haystack: $html);
        self::assertStringContainsString(needle: 'Connexion réservée aux membres de l\'APERP.', haystack: $html);
    }

    public function testLoginWithGoodCredentials(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');

        $crawler = $client
            ->request(
                method: Request::METHOD_GET,
                uri: $urlGenerator->generate(name: 'app_login')
            );

        $form = $crawler->filter(selector: 'form[name=login]')->form([
            'email' => 'pascal.briffard@aperp.fr',
            'password' => 'Password1234!',
        ]);

        $client->submit($form);

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FOUND);
        $client->followRedirect();

        self::assertRouteSame(expectedRoute: 'app_home');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
    }

    public function testLoginWithBadCredentials(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');

        $crawler = $client
            ->request(
                method: Request::METHOD_GET,
                uri: $urlGenerator->generate(name: 'app_login')
            );

        $form = $crawler->filter(selector: 'form[name=login]')->form([
            'email' => 'pascal.briffard@aperp.fr',
            'password' => 'BadPassword1234!',
        ]);

        $client->submit($form);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame(expectedRoute: 'app_login');
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);

        self::assertSelectorExists(selector: '#error-login');
    }

    public function testLogoutWork(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get(id: 'router');

        /** @var UserRepository $userRepository */
        $userRepository = $client
            ->getContainer()
            ->get(id: UserRepository::class);

        /** @var User $user */
        $user = $userRepository->findOneBy([]);

        $client->loginUser($user);

        $crawler = $client
            ->request(
                method: Request::METHOD_GET,
                uri: $urlGenerator->generate(name: 'app_logout')
            );

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame(expectedRoute: 'app_home');
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
    }
}
