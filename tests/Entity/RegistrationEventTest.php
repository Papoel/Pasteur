<?php

namespace App\Tests\Entity;

use App\Entity\Event\Children;
use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use App\Entity\Payment;
use DateTimeImmutable;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationEventTest extends KernelTestCase
{
    public function getEntityRegistrationEvent(): RegistrationEvent
    {
        // On instancie d'abord l'objet Event et on le set sur le RegistrationEvent
        $event = new Event();
        // ... set des propriétés de l'objet Event
        $event->setName(name: 'Event de test');
        $event->setDescription(description: 'Description de l\'event de test');
        $event->setPublished(published: true);
        $event->setCapacity(capacity: 10);
        $event->setStartsAt(startsAt: \DateTimeImmutable::createFromFormat(format: 'Y-m-d H:i:s', datetime: '2023-06-01 20:00:00'));
        $event->setFinishAt(finishAt: \DateTimeImmutable::createFromFormat(format: 'Y-m-d H:i:s', datetime: '2023-06-01 22:00:00'));
        $event->setPrice(price: 0);
        $event->setLocation(location: 'Lieu de l\'event de test');

        $registrationEvent = new RegistrationEvent();
        $registrationEvent->setFirstname(firstname: 'Papoel');
        $registrationEvent->setLastname(lastname: 'Briffard');
        $registrationEvent->setEmail(email: 'papoel@test.fr');
        $registrationEvent->setTelephone(telephone: '0606060606');
        $registrationEvent->setPaid(Paid: true);
        $registrationEvent->setEvent($event); // On associe l'objet Event à RegistrationEvent

        // On crée un objet Children et on l'ajoute à la collection d'enfants de RegistrationEvent
        $child1 = new Children();
        // ... set des propriétés de l'objet Children
        $child1->setFirstname(firstname: 'Prénom');
        $child1->setLastname(lastname: 'Nom');
        $child1->setClassroom(classroom: 'CP');
        // On ajoute l'objet Children à la collection d'enfants de RegistrationEvent
        $registrationEvent->getChildren()->add($child1);

        // On crée un objet Payment et on l'ajoute à la collection de paiements de RegistrationEvent
        $payment = new Payment();
        // ... set des propriétés de l'objet Payment
        $payment->setEvent($event); // On associe l'objet Event à Payment
        $payment->setRegistrationEvent($registrationEvent); // On associe l'objet RegistrationEvent à Payment
        $payment->setCreatedAt(createdAt: new \DateTimeImmutable());
        // Compter le nombre d'enfants inscrits à l'event
        $countChildren = $registrationEvent->getChildren()->count();
        // Calculer le montant total du paiement
        $totalAmount = $countChildren * $event->getPrice();
        $payment->setAmount(amount: $totalAmount);
        $payment->setStripePaymentIntentId(stripePaymentIntentId: 'pi_1J9Z2pK');
        $payment->setUnitPrice($event->getPrice());
        $payment->setReservedPlaces($countChildren);
        $payment->setStripePaymentIntentStatus(stripePaymentIntentStatus: 'succeeded');
        $payment->setStripeSessionId(stripeSessionId: 'cs_test_1J9Z2pK');
        // On ajoute l'objet Payment à la collection de paiements de RegistrationEvent
        $registrationEvent->getPayments()->add($payment);

        return $registrationEvent;
    }

    public function assertValidationErrorsCount(RegistrationEvent $entity, int $count): void
    {
        $validator = static::getContainer()->get(ValidatorInterface::class);
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
    public function entityUserIsValid(): void
    {
        $this->assertValidationErrorsCount($this->getEntityRegistrationEvent(), count: 0);
    }
    /** @test */
    public function firstnameIsString(): void
    {
        $registrationEventFirstname = $this->getEntityRegistrationEvent()->setFirstname(firstname: 'Firstname');
        self::assertIsString($registrationEventFirstname->getFirstname());

        $this->assertValidationErrorsCount(entity: $registrationEventFirstname, count: 0);
    }
    /** @test */
    public function firstnameIsGreaterThan50Characters(): void
    {
        $registrationEventFirstname = $this->getEntityRegistrationEvent()->setFirstname(firstname: str_repeat(string: 'a', times: 51));
        self::assertSame(expected: str_repeat(string: 'a', times: 51), actual: $registrationEventFirstname->getFirstname());

        $this->assertValidationErrorsCount(entity: $registrationEventFirstname, count: 1);
    }
    /** @test */
    public function firstnameIsSmallerThan3Characters(): void
    {
        $registrationEventFirstname = $this->getEntityRegistrationEvent()->setFirstname(firstname: 'aa');
        self::assertSame(expected: 'aa', actual: $registrationEventFirstname->getFirstname());

        $this->assertValidationErrorsCount(entity: $registrationEventFirstname, count: 1);
    }
    /** @test */
    public function lastnameIsGreaterThan50Characters(): void
    {
        $registrationEventLastname = $this->getEntityRegistrationEvent()->setLastname(lastname: str_repeat(string: 'a', times: 51));
        self::assertSame(expected: str_repeat(string: 'a', times: 51), actual: $registrationEventLastname->getLastname());

        $this->assertValidationErrorsCount(entity: $registrationEventLastname, count: 1);
    }
    /** @test */
    public function lastnameIsSmallerThan3Characters(): void
    {
        $registrationEventLastname = $this->getEntityRegistrationEvent()->setLastname(lastname: 'aa');
        self::assertSame(expected: 'aa', actual: $registrationEventLastname->getLastname());

        $this->assertValidationErrorsCount(entity: $registrationEventLastname, count: 1);
    }
    /** @test */
    public function emailIsBlank(): void
    {
        $registrationEventEmail = $this->getEntityRegistrationEvent()->setEmail(email: '');
        self::assertSame(expected: '', actual: $registrationEventEmail->getEmail());

        $this->assertValidationErrorsCount(entity: $registrationEventEmail, count: 1);
    }
    /** @test */
    public function emailIsValid(): void
    {
        $registrationEventEmail = $this->getEntityRegistrationEvent()->setEmail(email: 'papoel@email.fr');
        self::assertSame(expected: 'papoel@email.fr', actual: $registrationEventEmail->getEmail());

        $this->assertValidationErrorsCount($registrationEventEmail, count: 0);
    }
    /** @test */
    public function emailIsInvalid(): void
    {
        $registrationEventEmail = $this->getEntityRegistrationEvent()->setEmail(email: 'papoel.email.fr');
        self::assertSame(expected: 'papoel.email.fr', actual: $registrationEventEmail->getEmail());

        $this->assertValidationErrorsCount($registrationEventEmail, count: 1);
    }
    /** @test */
    public function telephoneIsGreaterThan10Characters(): void
    {
        $registrationEventTelephone = $this->getEntityRegistrationEvent()->setTelephone(telephone: '01234567890');
        self::assertSame(expected: '01234567890', actual: $registrationEventTelephone->getTelephone());

        $this->assertValidationErrorsCount(entity: $registrationEventTelephone, count: 1);
    }
    /** @test */
    public function telephoneIsSmallerThan10Characters(): void
    {
        $registrationEventTelephone = $this->getEntityRegistrationEvent()->setTelephone(telephone: '0123456');
        self::assertSame(expected: '0123456', actual: $registrationEventTelephone->getTelephone());

        $this->assertValidationErrorsCount(entity: $registrationEventTelephone, count: 1);
    }
    /** @test */
    public function telephoneIsNull(): void
    {
        $registrationEventTelephone = $this->getEntityRegistrationEvent()->setTelephone(telephone: null);
        self::assertNull($registrationEventTelephone->getTelephone());

        $this->assertValidationErrorsCount(entity: $registrationEventTelephone, count: 0);
    }
    /** @test */
    public function telephoneIsValid(): void
    {
        $registrationEventTelephone = $this->getEntityRegistrationEvent()->setTelephone(telephone: '0123456789');
        self::assertSame(expected: '0123456789', actual: $registrationEventTelephone->getTelephone());

        $this->assertValidationErrorsCount(entity: $registrationEventTelephone, count: 0);
    }
    /** @test */
    public function telephoneCanContainOnlyNumbers(): void
    {
        $registrationEventTelephone = $this->getEntityRegistrationEvent()->setTelephone(telephone: '0123456pa9');
        self::assertSame(expected: '0123456pa9', actual: $registrationEventTelephone->getTelephone());

        $this->assertValidationErrorsCount(entity: $registrationEventTelephone, count: 1);
    }
    /** @test */
    public function telephoneMustBeNumeric(): void
    {
        $registrationEventTelephone = $this->getEntityRegistrationEvent()->setTelephone(telephone: '0123456789a');
        self::assertIsNotNumeric(actual: $registrationEventTelephone->getTelephone());

        $this->assertValidationErrorsCount(entity: $registrationEventTelephone, count: 2);
    }
    /** @test */
    public function paidIsFalseByDefault(): void
    {
        $registrationEvent = new RegistrationEvent();
        self::assertFalse($registrationEvent->isPaid());
    }
    /** @test */
    public function addChild(): void
    {
        $registrationEvent = new RegistrationEvent();
        $registrationEvent->setFirstname(firstname: 'Papoel');
        $registrationEvent->setLastname(lastname: 'Briffard');
        $registrationEvent->setEmail(email: 'papoel@test.fr');

        $child = new Children();
        $child->setFirstname(firstname: 'Firstname');
        $child->setLastname(lastname: 'Lastname');
        $child->setClassroom(classroom: 'Extérieur');
        $child->setRegistrationEvent($registrationEvent);
        $child->setAge(age: 5);

        $registrationEvent->addChild($child);

        self::assertTrue($registrationEvent->getChildren()->contains($child));
        self::assertSame($registrationEvent, $child->getRegistrationEvent());
        self::assertCount(expectedCount: 1, haystack: $registrationEvent->getChildren());

        $this->assertValidationErrorsCount(entity: $registrationEvent, count: 0);
    }
    /** @test */
    public function removeChild(): void
    {
        $registrationEvent = new RegistrationEvent();
        $registrationEvent->setFirstname(firstname: 'Papoel');
        $registrationEvent->setLastname(lastname: 'Briffard');
        $registrationEvent->setEmail(email: 'papoel@test.fr');

        $child = new Children();
        $child->setFirstname(firstname: 'Firstname');
        $child->setLastname(lastname: 'Lastname');
        $child->setClassroom(classroom: 'Extérieur');
        $child->setRegistrationEvent($registrationEvent);
        $child->setAge(age: 5);

        $registrationEvent->addChild($child);
        self::assertCount(expectedCount: 1, haystack: $registrationEvent->getChildren());

        // Supprimer l'enfant de l'événement d'inscription
        $registrationEvent->removeChild($child);

        // Vérifier que l'enfant a été supprimé de l'événement d'inscription
        self::assertFalse($registrationEvent->getChildren()->contains($child));
        self::assertNull($child->getRegistrationEvent());
        self::assertCount(expectedCount: 0, haystack: $registrationEvent->getChildren());

        // Retourne l'erreur de validation: "Veuillez inscrire au moins 1 enfant."
        $this->assertValidationErrorsCount(entity: $registrationEvent, count: 1);
    }
    /** @test */
    public function getCreatedAtReturnsNullableDateTimeImmutable(): void
    {
        $registrationEvent = new RegistrationEvent();
        self::assertInstanceOf(expected: DateTimeImmutable::class, actual: $registrationEvent->getCreatedAt());
    }
    /** @test */
    public function setCreatedAtSetsValue(): void
    {
        $registrationEvent = new RegistrationEvent();
        $createdAt = new DateTimeImmutable(datetime: '2022-01-01 00:00:00');

        $registrationEvent->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $registrationEvent->getCreatedAt());
    }
    /** @test */
    public function getEvent(): void
    {
        $registrationEvent = new RegistrationEvent();
        $registrationEvent->setFirstname(firstname: 'Papoel');
        $registrationEvent->setLastname(lastname: 'Briffard');
        $registrationEvent->setEmail(email: 'papoel@test.fr');

        $event = new Event();
        $registrationEvent->setEvent($event);

        $child = new Children();
        $child->setFirstname(firstname: 'Firstname');
        $child->setLastname(lastname: 'Lastname');
        $child->setClassroom(classroom: 'Extérieur');
        $child->setRegistrationEvent($registrationEvent);
        $child->setAge(age: 5);

        $registrationEvent->addChild($child);

        self::assertTrue($registrationEvent->getChildren()->contains($child));
        self::assertSame($event, $registrationEvent->getEvent());

        $this->assertValidationErrorsCount($registrationEvent, count: 0);
    }
    /** @test */
    #[NoReturn] public function addPayment(): void
    {
        $event = new Event();

        $event->setName(name: 'Event de test payment');
        $event->setDescription(description: 'Description de l\'event de test payment');
        $event->setPublished(published: true);
        $event->setCapacity(capacity: 10);
        $event->setStartsAt(startsAt: \DateTimeImmutable::createFromFormat(
            format: 'Y-m-d H:i:s',
            datetime: '2023-06-01 20:00:00'
        ));
        $event->setFinishAt(finishAt: \DateTimeImmutable::createFromFormat(
            format: 'Y-m-d H:i:s',
            datetime: '2023-06-01 22:00:00'
        ));
        $event->setPrice(price: 300);
        $event->setLocation(location: 'Lieu de l\'event de test');

        $registrationEvent = new RegistrationEvent();

        $registrationEvent->setFirstname(firstname: 'Papoel');
        $registrationEvent->setLastname(lastname: 'Briffard');
        $registrationEvent->setEmail(email: 'papoel@test.fr');
        $registrationEvent->setTelephone(telephone: '0606060606');
        $registrationEvent->setPaid(Paid: false);
        $registrationEvent->setEvent($event);

        $child1 = new Children();

        $child1->setFirstname(firstname: 'Prénom1');
        $child1->setLastname(lastname: 'Nom1');
        $child1->setClassroom(classroom: 'CP');
        $child1->setAge(age: 6);

        $child2 = new Children();
        $child2->setFirstname(firstname: 'Prénom2');
        $child2->setLastname(lastname: 'Nom2');
        $child2->setClassroom(classroom: 'CE1');
        $child2->setAge(age: 8);

        $registrationEvent->getChildren()->add($child1);
        $registrationEvent->getChildren()->add($child2);

        $payment = new Payment();
        $payment->setRegistrationEvent(registrationEvent: $registrationEvent);
        $payment->setEvent(event: $event);
        $payment->setStripeSessionId(stripeSessionId: 'cs_test_1J9Z2pK');
        $payment->setCreatedAt(createdAt: new \DateTimeImmutable());
        $payment->setStripePaymentIntentId(stripePaymentIntentId: 'pi_1J9Z2pK');
        $payment->setStripePaymentIntentStatus(stripePaymentIntentStatus: 'succeeded');
        $countChildren = $registrationEvent->getChildren()->count();
        $payment->setReservedPlaces(reservedPlaces: $countChildren);
        $totalAmount = $countChildren * $event->getPrice();
        $payment->setAmount(amount: $totalAmount);
        $payment->setUnitPrice(unit_price: $event->getPrice());

        $registrationEvent->setPaid(Paid: true);

        dd($payment);

        $this->assertValidationErrorsCount($registrationEvent, count: 0);

        self::assertCount(expectedCount: 0, haystack: $registrationEvent->getPayments());
        $registrationEvent->getEvent()->addPayment($payment);
        self::assertTrue($registrationEvent->getPayments()->contains($payment));

        $this->assertValidationErrorsCount($registrationEvent, count: 0);

        self::assertTrue(condition: $event->getPayments()->contains($payment));
        self::assertSame(expected: $event, actual: $payment->getEvent());
        self::assertCount(expectedCount: 1, haystack: $registrationEvent->getPayments());

        // self::assertCount(expectedCount: 1, haystack: $event->getPayments());
    }
    /** @test */
    public function removePayment(): void
    {
        $registrationEvent = new RegistrationEvent();
        $registrationEvent->setFirstname(firstname: 'Papoel');
        $registrationEvent->setLastname(lastname: 'Briffard');
        $registrationEvent->setEmail(email: 'papoel@test.fr');

        $event = new Event();
        $registrationEvent->setEvent($event);

        $payment = new Payment();
        $

        $registrationEvent->getEvent($event)->addPayment($payment);

        dd($event->getPayments());

        self::assertCount(expectedCount: 1, haystack: $event->getPayments());

        $registrationEvent->getEvent()->removePayment($payment);

        self::assertCount(expectedCount: 0, haystack: $event->getPayments());
        self::assertFalse(condition: $event->getPayments()->contains($payment));
        self::assertNull(actual: $payment->getEvent());
    }

    public function testAddPayment(): void
    {
        $registrationEvent = new RegistrationEvent();
        $registrationEvent->setFirstname(firstname: 'Papoel');
        $registrationEvent->setLastname(lastname: 'Briffard');
        $registrationEvent->setEmail(email: 'papoel@test.fr');
        $registrationEvent->setTelephone(telephone: '0606060606');
        $registrationEvent->setPaid(Paid: true);

        $registrationEvent->setEvent($event);

        $event->setName(name: 'Event de test');
        $event->setDescription(description: 'Description de l\'event de test');
        $event->setPublished(published: true);
        $event->setCapacity(capacity: 10);
        $event->setStartsAt(startsAt: \DateTimeImmutable::createFromFormat(format: 'Y-m-d H:i:s', datetime: '2023-06-01 20:00:00'));
        $event->setFinishAt(finishAt: \DateTimeImmutable::createFromFormat(format: 'Y-m-d H:i:s', datetime: '2023-06-01 22:00:00'));
        $event->setPrice(price: 0);
        $event->setLocation(location: 'Lieu de l\'event de test');


        $payment = new Payment();
        // ... set des propriétés de l'objet Payment
        $payment->setEvent($event); // On associe l'objet Event à Payment
        $payment->setRegistrationEvent($registrationEvent); // On associe l'objet RegistrationEvent à Payment
        $payment->setCreatedAt(createdAt: new \DateTimeImmutable());

        $event->addPayment($payment);

        $this->assertTrue($event->getPayments()->contains($payment));
        $this->assertEquals($event, $payment->getEvent());
    }

    public function testRemovePayment(): void
    {

        $registrationEvent = new RegistrationEvent();
        $registrationEvent->setFirstname(firstname: 'Papoel');
        $registrationEvent->setLastname(lastname: 'Briffard');
        $registrationEvent->setEmail(email: 'papoel@test.fr');

        $event = new Event();

        $registrationEvent->setEvent($event);

        $payment = new Payment();
        $event->addPayment($payment);

        $event->removePayment($payment);

        $this->assertFalse($event->getPayments()->contains($payment));
        $this->assertNull($payment->getEvent());
    }
}
