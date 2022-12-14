<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Event\Event;
use App\Entity\User\User;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EventTest extends KernelTestCase
{
    public function getEntityEvent(): Event
    {
        $event = new Event();
        $event->setName(name: 'Coupe du monde 2026');
        $event->setDescription(
            description: 'La Coupe du monde de football est un événement sportif international qui se déroule 
            tous les quatre ans.
            Il s\'agit d\'un tournoi de football masculin organisé par 
            la Fédération Internationale de Football Association (FIFA). 
            32 équipes nationales qualifiées s\'affrontent pour remporter la coupe, symbolisée par la coupe Jules Rimet.
            Le tournoi est l\'un des plus grands événements sportifs au monde, avec des millions de téléspectateurs 
            et de fans suivant les matchs dans les stades et à la télévision. 
            La dernière édition de la Coupe du monde de football a eu lieu en 2022 au Qatar, et la prochaine édition 
            se déroulera en 2026 et sera organisé conjointement par 16 villes de trois pays nord-américains : 
            les Etats-Unis, le Canada, et le Mexique.'
        );
        $event->setLocation(location: 'Amérique du Nord');
        $event->setPrice(price: 5.00);
        $event->setStartsAt(new DateTimeImmutable(datetime: '08-06-2026'));
        $event->setFinishAt(new DateTimeImmutable(datetime: '13-07-2026'));
        $event->setCapacity(random_int(0, 500));
        $event->setHelpNeeded(helpNeeded: true);

        return $event;
    }

    public function assertValidationErrorsCount(Event $entity, int $count): void
    {
        $validator = static::getContainer()->get(ValidatorInterface::class);
        $violations = $validator->validate($entity);

        $messages = [];
        foreach ($violations as $violation) {
            $messages[] =
                'Erreur sur la Propriété '
                . ucfirst($violation->getPropertyPath()) . ' => ' . $violation->getMessage();
        }

        $this->assertCount(
            expectedCount: $count,
            haystack: $violations,
            message: implode(separator:PHP_EOL, array: $messages),
        );
    }

    public function testEntityUserIsValid(): void
    {
        $this->assertValidationErrorsCount($this->getEntityEvent(), count: 0);
    }

    public function testNameIsEmpty(): void
    {
        $eventName = $this->getEntityEvent()->setName(name: '');
        self::assertSame(expected: '', actual: $eventName->getName());
        self::assertEmpty(actual: $eventName->getName());

        // Erreur 1 => NotBlank
        // Erreur 2 => min: 3
        $this->assertValidationErrorsCount($eventName, count: 2);
    }

    public function testNameIsLessThan3Characters(): void
    {
        $eventName = $this->getEntityEvent()
            ->setName(name: '&!')
        ;
        $lgName = strlen($eventName->getName());
        $expected = 3;

        self::assertLessThan(
            expected: $expected,
            actual: strlen($eventName->getName()),
            message:
                'Longueur calculée : '
                . $lgName .
                ' => Il est attendu - de ' . $expected . ' caractères.'
        );

        $this->assertValidationErrorsCount($eventName, count: 1);
    }

    public function testNameIsGreaterThan150Characters(): void
    {
        $expected = 150;
        $eventName = $this->getEntityEvent()
            ->setName(name: str_repeat(string: 'a', times: $expected + 1));
        $lgName = strlen($eventName->getName());

        self::assertGreaterThan(
            expected: $expected,
            actual: strlen($eventName->getName()),
            message:
                'Longueur calculée : '
                . $lgName .
                ' => Il est attendu + de ' . $expected . ' caractères.'
        );

        $this->assertValidationErrorsCount($eventName, count: 1);
    }

    public function testDescriptionIsBlank(): void
    {
        $eventDescription = $this->getEntityEvent()
            ->setDescription(description: '');

        self::assertEmpty(
            actual: $eventDescription->getDescription(),
            message: 'Une valeur vide est attendue, or Description = "' . $eventDescription->getDescription() . '"',
        );

        $this->assertValidationErrorsCount($eventDescription, count: 1);
    }

    public function testLocationIsEmpty(): void
    {
        $eventLocation = $this->getEntityEvent()
            ->setLocation(location: '');

        self::assertEmpty(
            actual: $eventLocation->getLocation(),
            message: 'Une valeur vide est attendue, or Description = "' . $eventLocation->getLocation() . '"',
        );

        // Erreur 1 => Not Blank
        // Erreur 2 => min : 3
        $this->assertValidationErrorsCount($eventLocation, count: 2);
    }

    public function testLocationIsLessThan3Characters(): void
    {
        $expected = 3;
        $eventLocation = $this->getEntityEvent()
            ->setLocation(location: '&!')
        ;
        $lgName = strlen($eventLocation->getLocation());

        self::assertLessThan(
            expected: $expected,
            actual: strlen($eventLocation->getLocation()),
            message:
            'Longueur calculée : '
            . $lgName .
            ' => Il est attendu ' . $expected . ' caractères.'
        );

        $this->assertValidationErrorsCount($eventLocation, count: 1);
    }

    public function testLocationIsGreaterThan100Characters(): void
    {
        $expected = 100;
        $eventLocation = $this->getEntityEvent()
            ->setLocation(location: str_repeat(string: 'a', times: $expected + 1));
        $lgName = strlen($eventLocation->getLocation());

        self::assertGreaterThan(
            expected: $expected,
            actual: strlen($eventLocation->getLocation()),
            message:
            'Longueur calculée : '
            . $lgName .
            ' => Il est attendu + de ' . $expected . ' caractères.'
        );

        $this->assertValidationErrorsCount($eventLocation, count: 1);
    }

    public function testPriceIsLessThan0(): void
    {
        $expected = 0;
        $eventPrice = $this->getEntityEvent()->setPrice(price: $expected - 1);

        $this->assertGreaterThanOrEqual(expected: $expected - 1, actual: $eventPrice->getPrice());

        $this->assertValidationErrorsCount($eventPrice, count: 1);
    }

    public function testPriceIsGreaterThan100(): void
    {
        $expected = 99;
        $eventPrice = $this->getEntityEvent()->setPrice(price: $expected + 1);


        $this->assertGreaterThanOrEqual(expected: $expected + 1, actual: $eventPrice->getPrice());

        $this->assertValidationErrorsCount($eventPrice, count: 1);
    }

    public function testEndDateEventIsBeforeThanStartDateEvent(): void
    {
        $startEvent = new \DateTimeImmutable(datetime: 'now');
        $endEvent = new \DateTimeImmutable(datetime: 'now - 1 day');

        $eventStartsAt = $this->getEntityEvent()->setStartsAt($startEvent);
        $eventFinishAt = $this->getEntityEvent()->setFinishAt($endEvent);

        self::assertGreaterThan($endEvent, $eventStartsAt->getStartsAt());

        $this->assertValidationErrorsCount(entity: $eventFinishAt, count: 1);
    }

    // TODO: Comment tester l'entrée d'une valeur null.
    /*public function testDateStartAndEndEventAreBlank(): void
    {
        $startEvent = null;
        $endEvent = null;

        $eventStartsAt = $this->getEntityEvent()->setStartsAt($startEvent);
        $eventFinishAt = $this->getEntityEvent()->setFinishAt($endEvent);

        self::assertNull(actual: $startEvent, message: $eventStartsAt->getStartsAt());
        self::assertNull(actual: $endEvent, message: $eventFinishAt->getFinishAt());

        $this->assertValidationErrorsCount($eventStartsAt, count: 1);
    }*/

    // TODO: Comment tester le type.
    /*public function testNameIsString(): void
    {
        $eventName = $this->getEntityEvent()->setName(name: 123);
        self::assertIsString(actual: $eventName->getName());

        $this->assertValidationErrorsCount($eventName, count: 1);
    }*/
}
