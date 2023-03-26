<?php

namespace App\Tests\Entity;

use App\Entity\Order\Order;
use App\Entity\Order\OrderDetails;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderDetailsTest extends KernelTestCase
{
    private ValidatorInterface $validator;
    private $container;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->validator = static::getContainer()->get(id: ValidatorInterface::class);
    }

    public function getEntityOrderDetails(): OrderDetails
    {
        // Création d'une commande
        $order = new Order();
        $order->setFirstname(firstname: 'Bruce');
        $order->setLastname(lastname: 'Wayne');
        $order->setTelephone(telephone: '0123456789');
        $order->setEmail(email: 'batman@gotham.city');
        $order->setPaid(paid: true);
        $order->setCreatedAt(createdAt: new DateTimeImmutable());
        $order->setStripePaymentIntentId(stripePaymentIntentId: 'cs_test_123456789');

        // Création des détails de la commande
        $orderDetails = new OrderDetails();
        $orderDetails->setMyOrder($order);
        $orderDetails->setProduct(product: 'Test Product');
        $orderDetails->setQuantity(quantity: 1);
        $orderDetails->setPrice(price: 1000);
        $orderDetails->setTotal(total: 1000);
        $orderDetails->setProductId(productId: null);
        return $orderDetails;
    }

    public function assertValidationErrorsCount(OrderDetails $entity, int $count): void
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
    public function entity_is_valid(): void
    {
        $orderDetails = $this->getEntityOrderDetails();
        $violations = $this->validator->validate($orderDetails);

        self::assertCount(expectedCount: 0, haystack: $violations);
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 1 - Erreur sur la Propriété Product => Le produit doit être renseigné.
     * @test
     */
    public function product_is_blank(): void
    {
        $orderDetails = $this->getEntityOrderDetails();
        $orderDetails->setProduct(product: '');

        $violations = $this->validator->validate($orderDetails);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(expected: 'Le produit doit être renseigné', actual: $violations[0]->getMessage());
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 1 - Erreur sur la Propriété Quantité => La quantité doit > 0
     * @test
     */
    public function quantity_is_equal_at_0(): void
    {
        $orderDetails = $this->getEntityOrderDetails();
        $orderDetails->setQuantity(quantity: 0);

        $violations = $this->validator->validate($orderDetails);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(expected: 'La quantité doit être supérieure à 0', actual: $violations[0]->getMessage());
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 1 - Erreur sur la Propriété Quantité => La quantité doit être supérieure à 0
     * @test
     */
    public function quantity_is_negative(): void
    {
        $orderDetails = $this->getEntityOrderDetails();
        $orderDetails->setQuantity(quantity: -8);

        $violations = $this->validator->validate($orderDetails);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(expected: 'La quantité doit être supérieure à 0', actual: $violations[0]->getMessage());
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 1 - Erreur sur la Propriété Prix => Le prix doit être > 0
     * @test
     */
    public function price_is_equal_at_0(): void
    {
        $orderDetails = $this->getEntityOrderDetails();
        $orderDetails->setPrice(price: 0);

        $violations = $this->validator->validate($orderDetails);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(expected: 'Le prix doit être supérieure à 0', actual: $violations[0]->getMessage());
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 1 - Erreur sur la Propriété Prix => Le prix doit être supérieure à 0
     * @test
     */
    public function price_is_negative(): void
    {
        $orderDetails = $this->getEntityOrderDetails();
        $orderDetails->setPrice(price: -8);

        $violations = $this->validator->validate($orderDetails);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(expected: 'Le prix doit être supérieure à 0', actual: $violations[0]->getMessage());
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 1 - Erreur sur la Propriété Total => Le total doit être > 0
     * @test
     */
    public function total_is_equal_at_0(): void
    {
        $orderDetails = $this->getEntityOrderDetails();
        $orderDetails->setTotal(total: 0);

        $violations = $this->validator->validate($orderDetails);

        self::assertCount(expectedCount: 1, haystack: $violations);
        self::assertSame(expected: 'Le total doit être supérieure à 0', actual: $violations[0]->getMessage());
    }

    /**
     * Nombre d'erreurs attendues : 1
     * - 1 - Erreur sur la Propriété Total => Le total doit être supérieure à 0
     * @test
     */
    public function total_is_negative(): void
    {
        $orderDetails = $this->getEntityOrderDetails();
        $orderDetails->setTotal(total: -8);

        $violations = $this->validator->validate($orderDetails);

        self::assertCount(expectedCount: 1 , haystack: $violations);
        self::assertSame(expected: 'Le total doit être supérieure à 0' , actual: $violations[0]->getMessage());
    }
}
