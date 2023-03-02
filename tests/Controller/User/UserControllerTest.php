<?php

namespace App\Tests\Controller\User;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $repository;
    private string $path = '/user/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()
            ->get(id: 'doctrine')
            ->getRepository(persistentObject: User::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove(entity: $object, flush: true);
        }
    }

    /** @test */
    public function IndexUser(): void
    {
        $this->markTestIncomplete();
        $this->client->request(
            method: Request::METHOD_GET,
            uri: $this->client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_user_index', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FOUND, message: 'La réponse n\'est pas 302');
        self::assertPageTitleContains(expectedTitle: 'User index');
    }

    /** @test */
    public function NewUser(): void
    {
        $this->markTestIncomplete();
        $this->client->request(
            method: Request::METHOD_GET,
            uri: $this->client->getContainer()
                ->get(id: 'router')
                ->generate(name: 'app_user_new', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $originalNumObjectsInRepository = count($this->repository->findAll());

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK, message: 'The response status code is not 200');


        $this->client->submitForm('Save', [
            'user[email]' => 'Testing',
            'user[roles]' => 'Testing',
            'user[password]' => 'Testing',
            'user[firstname]' => 'Testing',
            'user[lastname]' => 'Testing',
            'user[pseudo]' => 'Testing',
            'user[telephone]' => 'Testing',
            'user[address]' => 'Testing',
            'user[complementAddress]' => 'Testing',
            'user[postalCode]' => 'Testing',
            'user[town]' => 'Testing',
            'user[createdAt]' => 'Testing',
            'user[updatedAt]' => 'Testing',
            'user[birthday]' => 'Testing',
            'user[function]' => 'Testing',
            'user[isVerified]' => 'Testing',
            'user[agreedTermsAt]' => 'Testing',
            'user[askDeleteAccountAt]' => 'Testing',
        ]);

        self::assertResponseRedirects(expectedLocation: '/user/');

        self::assertCount(expectedCount: $originalNumObjectsInRepository + 1, haystack: $this->repository->findAll());
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setEmail('testeur@example.com');
        $fixture->setRoles(['ROLE_USER']);
        $fixture->setPassword('Password');
        $fixture->setFirstname('Jean');
        $fixture->setLastname('Test');
        $fixture->setPseudo('Big Tasty');
        $fixture->setTelephone('0102030405');
        $fixture->setAddress('37 rue des tests');
        $fixture->setComplementAddress('Mock 666');
        $fixture->setPostalCode('59666');
        $fixture->setTown('Unit');
        $fixture->setCreatedAt(new \DateTimeImmutable('2023-03-01 00:00:00'));
        $fixture->setUpdatedAt(null);
        $fixture->setBirthday(new \DateTime('2001-06-13'));
        $fixture->setFunction(null);
        $fixture->setIsVerified(true);
        $fixture->setAgreedTermsAt(new \DateTimeImmutable('2023-03-01 00:05:37'));
        $fixture->setAskDeleteAccountAt(askDeleteAccountAt: null);

        $this->repository->save($fixture, flush: true);

        $this->client->request(
            method: Request::METHOD_GET,
            uri: $this->client->getContainer()
                ->get(id: 'router')
                ->generate(
                    name: 'app_user_show',
                    parameters: ['id' => $fixture->getId()],
                    referenceType: UrlGeneratorInterface::ABSOLUTE_URL
                )
        );

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertPageTitleContains('User', message: 'The page title is not "User"');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEditUser(): void
    {
        $this->markTestIncomplete();

        $fixture = new User();
        $fixture->setEmail('testeur@example.com');
        $fixture->setRoles(['ROLE_USER']);
        $fixture->setPassword('Password');
        $fixture->setFirstname('Jean');
        $fixture->setLastname('Test');
        $fixture->setPseudo('Big Tasty');
        $fixture->setTelephone('0102030405');
        $fixture->setAddress('37 rue des tests');
        $fixture->setComplementAddress('Mock 666');
        $fixture->setPostalCode('59666');
        $fixture->setTown('Unit');
        $fixture->setCreatedAt(createdAt: new \DateTimeImmutable(datetime: '2023-03-01 00:00:00'));
        $fixture->setUpdatedAt(null);
        $fixture->setBirthday(new \DateTime('2001-06-13'));
        $fixture->setFunction(null);
        $fixture->setIsVerified(true);
        $fixture->setAgreedTermsAt(new \DateTimeImmutable('2023-03-01 00:05:37'));
        $fixture->setAskDeleteAccountAt(null);

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm(button: 'Update', fieldValues: [
            'user[email]' => 'new_email@mail.com',
            'user[roles]' => '[ROLE_ADMIN]',
            'user[password]' => 'new_password',
            'user[firstname]' => 'Jean',
            'user[lastname]' => 'Test',
            'user[pseudo]' => 'Big Tasty',
            'user[telephone]' => '0102030405',
            'user[address]' => '37 rue des tests',
            'user[complementAddress]' => 'Mock 666',
            'user[postalCode]' => '59666',
            'user[town]' => 'Unit',
            'user[birthday]' => '2001-06-13',
            'user[isVerified]' => true,
            'user[agreedTermsAt]' => '2023-03-01 00:05:37',
        ]);

        self::assertResponseRedirects(expectedLocation: '/user/');

        $updatedUser = $this->repository->findOneBy(['email' => 'new_email@mail.com']);

        self::assertSame(expected: 'new_email@mail.com', actual: $updatedUser->getEmail());
        self::assertSame(expected: '[ROLE_ADMIN]', actual: $updatedUser->getRoles());
        self::assertSame(expected: 'new_password', actual: $updatedUser->getPassword());
        self::assertSame(expected: 'Jean', actual:  $updatedUser->getFirstname());
        self::assertSame(expected: 'Test', actual: $updatedUser->getLastname());
        self::assertSame(expected: 'Big Tasty', actual:  $updatedUser->getPseudo());
        self::assertSame(expected: '0102030405', actual:  $updatedUser->getTelephone());
        self::assertSame(expected: '37 rue des tests', actual:  $updatedUser->getAddress());
        self::assertSame(expected: 'Mock 666', actual:  $updatedUser->getComplementAddress());
        self::assertSame(expected: '59666', actual:  $updatedUser->getPostalCode());
        self::assertSame(expected: 'Unit', actual:  $updatedUser->getTown());
        self::assertSame(expected: '2001-06-13', actual: $updatedUser->getBirthday());
        self::assertTrue(condition: $updatedUser->getIsVerified());
        self::assertSame(expected: '2023-03-01 00:05:37', actual:  $updatedUser->getAgreedTermsAt());
        self::assertNull(actual: $updatedUser->getFunction());
        self::assertNull(actual: $updatedUser->getAskDeleteAccountAt());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new User();
        $fixture->setEmail('My Title');
        $fixture->setRoles('My Title');
        $fixture->setPassword('My Title');
        $fixture->setFirstname('My Title');
        $fixture->setLastname('My Title');
        $fixture->setPseudo('My Title');
        $fixture->setTelephone('My Title');
        $fixture->setAddress('My Title');
        $fixture->setComplementAddress('My Title');
        $fixture->setPostalCode('My Title');
        $fixture->setTown('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setBirthday('My Title');
        $fixture->setFunction('My Title');
        $fixture->setIsVerified('My Title');
        $fixture->setAgreedTermsAt('My Title');
        $fixture->setAskDeleteAccountAt('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/user/');
    }
}
