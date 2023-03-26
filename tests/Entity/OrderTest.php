<?php

namespace App\Tests\Entity;

use App\Entity\Order\Order;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderTest extends KernelTestCase
{
    private ValidatorInterface $validator;
    private $container;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->validator = static::getContainer()->get(id: ValidatorInterface::class);
    }

    public function getEntityOrder(): Order
    {
        $order = new Order();
        $order->setFirstname(firstname: 'Bruce');
        $order->setLastname(lastname: 'Wayne');
        $order->setTelephone(telephone: '0123456789');
        $order->setEmail(email: 'batman@gotham.city');
        $order->setPaid(paid: true);
        $order->setCreatedAt(createdAt: new DateTimeImmutable());
        $order->setStripePaymentIntentId(stripePaymentIntentId: 'cs_test_123456789');
        return $order;
    }

    public function assertValidationErrorsCount(Order $entity, int $count): void
    {
        $validator = $this->container->get(id: ValidatorInterface::class);
        $violations = $validator->validate($entity);

        $messages = [];
        foreach ($violations as $violation) {
            $messages[] =
                'Erreur sur la Propriété '
                . ucfirst($violation->getPropertyPath()) . ' => ' . $violation->getMessage();
        }

        self::assertCount(
            expectedCount: $count,
            haystack: $violations,
            message: implode(separator:PHP_EOL, array: $messages)
        );
    }

    /** @test */
    public function entity_order_is_valid(): void
    {
        $order = $this->getEntityOrder();
        $violations = $this->validator->validate($order);
        self::assertCount(expectedCount: 0, haystack: $violations);
    }

    /**
     * Nombre d'erreurs attendues : 2
     * - 1 - Erreur sur la Propriété Firstname => Le prénom est obligatoire.
     * - 2 - Erreur sur la Propriété Lastname => Le prénom doit faire au moins 3 caractères.
     * @test
     */
    public function entity_order_is_invalid(): void
    {
        $order = $this->getEntityOrder();
        $order->setFirstname(firstname: '');

        $violations = $this->validator->validate($order);
        self::assertCount(expectedCount: 2, haystack: $violations);
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 2 - Erreur sur la Propriété Lastname => Le prénom doit faire au plus 3 caractères.
     * @test
     */
    public function firstname_is_less_than_3_characters(): void
    {
        $order = $this->getEntityOrder();
        $order->setFirstname(firstname: 'a');

        $violations = $this->validator->validate($order);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(expected: 'Le prénom doit faire au moins 3 caractères', actual: $violations[0]->getMessage());
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 2 - Erreur sur la Propriété Lastname => Le prénom doit faire au plus 50 caractères.
     * @test
     */
    public function firstname_is_more_than_50_characters(): void
    {
        $order = $this->getEntityOrder();
        $order->setFirstname(firstname: str_repeat(string: 'a', times: 51));

        $violations = $this->validator->validate($order);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(expected: 'Le prénom doit faire au plus 50 caractères', actual: $violations[0]->getMessage());
    }

    /**
     * Nombre d'erreurs attendues : 2
     * - 1 - Erreur sur la Propriété Lastname => Le nom est obligatoire.
     * - 2 - Erreur sur la Propriété Lastname => Le nom doit faire au moins 3 caractères.
     * @test
     */
    public function lastname_is_empty(): void
    {
        $order = $this->getEntityOrder();
        $order->setLastname(lastname: '');

        $violations = $this->validator->validate($order);

        self::assertCount(expectedCount: 2, haystack: $violations);

        self::assertSame(expected: 'Le nom doit faire au moins 3 caractères', actual: $violations[0]->getMessage());
        self::assertSame(expected: 'Le nom est obligatoire', actual: $violations[1]->getMessage());
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 2 - Erreur sur la Propriété Lastname => Le nom de famille doit faire au moins 3 caractères.
     * @test
     */
    public function lastname_is_less_than_3_characters(): void
    {
        $order = $this->getEntityOrder();
        $order->setLastname(lastname: 'a');

        $violations = $this->validator->validate($order);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(expected: 'Le nom doit faire au moins 3 caractères', actual: $violations[0]->getMessage());
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 1 - Erreur sur la Propriété Lastname => Le nom doit faire au plus 50 caractères.
     * @test
     */
    public function lastname_is_more_than_50_characters(): void
    {
        $order = $this->getEntityOrder();
        $order->setLastname(lastname: str_repeat(string: 'a', times: 51));

        $violations = $this->validator->validate($order);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(expected: 'Le nom doit faire au plus 50 caractères', actual: $violations[0]->getMessage());
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 1 - Erreur sur la Propriété Telephone => Le numéro de téléphone doit comporter 10 chiffres.
     * @test
     */
    public function telephone_is_less_than_10_characters(): void
    {
        $order = $this->getEntityOrder();
        $order->setTelephone(telephone: '012345678');

        $violations = $this->validator->validate($order);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(
            expected: 'Le numéro de téléphone doit comporter 10 chiffres.',
            actual: $violations[0]->getMessage()
        );
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 1 - Erreur sur la Propriété Telephone => Seuls les chiffres sont acceptés.
     * @test
     */
    public function telephone_is_not_a_number(): void
    {
        $order = $this->getEntityOrder();
        $order->setTelephone(telephone: '012345678a');

        $violations = $this->validator->validate($order);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(
            expected: 'Seuls les chiffres sont acceptés.',
            actual: $violations[0]->getMessage()
        );
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 1 - Erreur sur la Propriété Telephone => L'email est obligatoire.
     * @test
     */
    public function email_is_empty(): void
    {
        $order = $this->getEntityOrder();
        $order->setEmail(email: '');

        $violations = $this->validator->validate($order);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(
            expected: 'L\'email est obligatoire',
            actual: $violations[0]->getMessage()
        );
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 1 - Erreur sur la Propriété Telephone => L\' adresse email "{{ value }}" n\'est pas valide'.
     * @test
     */
    public function email_is_not_valid(): void
    {
        $order = $this->getEntityOrder();
        $order->setEmail(email: 'test');
        $mail = $order->getEmail();

        $violations = $this->validator->validate($order);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(
            expected: 'L\' adresse email "' .$mail. '" n\'est pas valide',
            actual: $violations[0]->getMessage()
        );
    }

    /**
     * Vérifier que la date de création est une instance de DateTimeImmutable.
     * @test
     */
    public function createdAt_is_instance_of_DateTimeImmutable(): void
    {
        $order = $this->getEntityOrder();
        self::assertInstanceOf(expected: DateTimeImmutable::class, actual: $order->getCreatedAt());
    }

}
