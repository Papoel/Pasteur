<?php

namespace App\Tests\Entity;

use App\Entity\Event\RegistrationHelp;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationTest extends KernelTestCase
{
    public function getEntityRegistration(): RegistrationHelp
    {
        $registration = new RegistrationHelp();
        $registration->setName(name: 'El Batman !');
        $registration->setEmail(email: 'batman@gotham.city');
        $phone = '0123456789';
        $registration->setTelephone(telephone: $phone);
        $registration->setActivity(activity: 'Vente');
        $registration->setMessage('Je souhaite venir vous aider.');
        //$registration->setEvent(event: $this->eventRepository->findOneBy(['id' => 1]));

        return $registration;
    }

    public function assertValidationErrorsCount(RegistrationHelp $entity, int $count): void
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
            message: implode(separator:PHP_EOL, array: $messages)
        );
    }

    public function testEntityRegistrationIsValid(): void
    {
        $this->assertValidationErrorsCount($this->getEntityRegistration(), count: 0);
    }

    public function testNameIsGreaterThan100(): void
    {
        $expected = 100;
        $registrationName = $this->getEntityRegistration()
            ->setName(name: str_repeat(string: 'a', times: $expected + 1));

        $lgName = strlen($registrationName->getName());

        self::assertGreaterThan(
            expected: $expected,
            actual: strlen($registrationName->getName()),
            message:
            'Longueur calculée : '
            . $lgName .
            ' => Il est attendu + de ' . $expected . ' caractères.'
        );

        $this->assertValidationErrorsCount($registrationName, count: 1);
    }

    public function testNameIsBlank(): void
    {
        $registrationName = $this->getEntityRegistration()
            ->setName(name: '');

        self::assertEmpty(
            actual: $registrationName->getName(),
            message: 'Une valeur vide est attendue, or Name = "' . $registrationName->getName() . '"',
        );

        $this->assertValidationErrorsCount($registrationName, count: 1);
    }

    public function testEmailIsBlank(): void
    {
        $registrationEmail = $this->getEntityRegistration()->setEmail(email: '');
        self::assertSame(expected: '', actual: $registrationEmail->getEmail());

        $this->assertValidationErrorsCount($registrationEmail, count: 1);
    }

    public function testEmailIsValid(): void
    {
        $registrationEmail = $this->getEntityRegistration()->setEmail(email: 'papoel@email.fr');
        self::assertSame(expected: 'papoel@email.fr', actual: $registrationEmail->getEmail());

        $this->assertValidationErrorsCount($registrationEmail, count: 0);
    }

    public function testEmailIsInvalid(): void
    {
        $registrationEmail = $this->getEntityRegistration()->setEmail(email: 'papoel.email.fr');
        self::assertSame(expected: 'papoel.email.fr', actual: $registrationEmail->getEmail());

        $this->assertValidationErrorsCount($registrationEmail, count: 1);
    }

    public function testEmailIsEmpty(): void
    {
        $registrationEmail = $this->getEntityRegistration()->setEmail(email: '');
        self::assertSame(expected: '', actual: $registrationEmail->getEmail());
        self::assertEmpty(actual: $registrationEmail->getEmail());

        $this->assertValidationErrorsCount($registrationEmail, count: 1);
    }

    public function testEmailIsGreaterThan180Characters(): void
    {
        $expected = 172;

        $registrationEmail = $this->getEntityRegistration()
            ->setEmail(email: str_repeat(string: 'a', times: $expected) . '@email.fr');
        $lgName = strlen($registrationEmail->getEmail());

        self::assertGreaterThan(
            expected: $expected,
            actual: strlen($registrationEmail->getEmail()),
            message:
            'Longueur calculée : '
            . $lgName .
            ' => Il est attendu + de ' . $expected . ' caractères.'
        );

        $this->assertValidationErrorsCount($registrationEmail, count: 1);
    }

    public function testActivityIsBlank(): void
    {
        $registrationActivity = $this->getEntityRegistration()
            ->setActivity(activity: '');

        self::assertEmpty(
            actual: $registrationActivity->getActivity(),
            message: 'Une valeur vide est attendue, or Activity = "' . $registrationActivity->getActivity() . '"',
        );

        $this->assertValidationErrorsCount($registrationActivity, count: 1);
    }
}
