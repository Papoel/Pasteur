<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Contact\Contact;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactTest extends KernelTestCase
{
    public function getEntityContact(): Contact
    {
        $contact = new Contact();
        $contact->setFullName(fullName: 'Bruce Wayne');
        $contact->setEmail(email: 'batman@gotham.city');
        $contact->setSubject('Le joker attaque !');
        $contact->setMessage(message: '
            Attention ! Le Joker a lancé une attaque surprise dans la ville. 
            Il est armé et dangereux. 
            Si vous êtes dans les environs, restez à l\'intérieur et barricadez les portes. 
            Si vous êtes en contact avec des proches, assurez-vous qu\'ils sont en sécurité. 
            Si possible, contactez les autorités pour leur signaler la situation.
        ');

        return $contact;
    }

    public function assertValidationErrorsCount(Contact $entity, int $count): void
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

    public function testEntityContactIsValid(): void
    {
        $this->assertValidationErrorsCount($this->getEntityContact(), count: 0);
    }

    public function testEmailIsBlank(): void
    {
        $contactEmail = $this->getEntityContact()->setEmail(email: '');
        self::assertSame(expected: '', actual: $contactEmail->getEmail());

        $this->assertValidationErrorsCount($contactEmail, count: 1);
    }

    public function testEmailIsValid(): void
    {
        $contactEmail = $this->getEntityContact()->setEmail(email: 'papoel@email.fr');
        self::assertSame(expected: 'papoel@email.fr', actual: $contactEmail->getEmail());

        $this->assertValidationErrorsCount($contactEmail, count: 0);
    }

    public function testEmailIsInvalid(): void
    {
        $contactEmail = $this->getEntityContact()->setEmail(email: 'papoel.email.fr');
        self::assertSame(expected: 'papoel.email.fr', actual: $contactEmail->getEmail());

        $this->assertValidationErrorsCount($contactEmail, count: 1);
    }

    public function testEmailIsEmpty(): void
    {
        $contactEmail = $this->getEntityContact()->setEmail(email: '');
        self::assertSame(expected: '', actual: $contactEmail->getEmail());
        self::assertEmpty(actual: $contactEmail->getEmail());

        $this->assertValidationErrorsCount($contactEmail, count: 1);
    }

    public function testEmailIsGreaterThan180Characters(): void
    {
        $contactEmail = $this->getEntityContact()->setEmail(email: str_repeat(string: 'a', times: 172) . '@email.fr');
        self::assertSame(
            expected: str_repeat(string: 'a', times: 172) . '@email.fr',
            actual: $contactEmail->getEmail()
        );

        $this->assertValidationErrorsCount($contactEmail, count: 1);
    }

    public function testFullnameIsNull(): void
    {
        $fullName = $this->getEntityContact()->setFullName(fullName: null);
        self::assertNull(actual: $fullName->getFullName());

        $this->assertValidationErrorsCount($fullName, count: 0);
    }

    public function testFullnameIsEmpty(): void
    {
        $fullName = $this->getEntityContact()->setFullName(fullName: '');
        self::assertEmpty(actual: $fullName->getFullName());

        $this->assertValidationErrorsCount($fullName, count: 0);
    }

    public function testFullnameIsNotNullOrEmpty(): void
    {
        $fullName = $this->getEntityContact()->setFullName(fullName: 'Batman');
        self::assertNotNull(actual: $fullName->getFullName());
        self::assertNotEmpty(actual: $fullName->getFullName());

        $this->assertValidationErrorsCount($fullName, count: 0);
    }

    public function testFullnameIsGreaterThan50Characters(): void
    {
        $fullName = $this->getEntityContact()->setFullName(fullName: str_repeat(string: 'a', times: 51));
        self::assertGreaterThan(expected: 50, actual: $fullName->getFullName());

        $this->assertValidationErrorsCount($fullName, count: 1);
    }

    public function testFullnameIsString(): void
    {
        $userFullName = $this->getEntityContact()->setFullName(fullName: 'Firstname');
        self::assertIsString($userFullName->getFullName());

        $this->assertValidationErrorsCount(entity: $userFullName, count: 0);
    }

    // TODO: Comment tester l'insertion d'un integer pour j'attend une erreur ...
    /*public function testFullnameIsInteger(): void
    {
        $userFullName = $this->getEntityContact()->setFullName(fullName: 12345);
        self::assertIsString($userFullName->getFullName());

        $this->assertValidationErrorsCount(entity: $userFullName, count: 1);
    }*/

    public function testSubjectIsNull(): void
    {
        $subject = $this->getEntityContact()->setSubject(subject: null);
        self::assertNull($subject->getSubject());

        $this->assertValidationErrorsCount(entity: $subject, count: 0);
    }

    public function testSubjectIsEmpty(): void
    {
        $subject = $this->getEntityContact()->setSubject(subject: '');
        self::assertEmpty($subject->getSubject());

        $this->assertValidationErrorsCount(entity: $subject, count: 0);
    }

    public function testSubjectIsNotNullOrEmpty(): void
    {
        $subject = $this->getEntityContact()->setFullName(fullName: 'Un sujet de test ?');
        self::assertNotNull(actual: $subject->getFullName());
        self::assertNotEmpty(actual: $subject->getFullName());

        $this->assertValidationErrorsCount($subject, count: 0);
    }

    public function testSubjectIsGreaterThan100Characters(): void
    {
        $subject = $this->getEntityContact()->setSubject(subject: str_repeat(string: 'a', times: 169));
        self::assertGreaterThan(expected: 100, actual: strlen($subject->getSubject()));

        $this->assertValidationErrorsCount($subject, count: 1);
    }

    public function testMessageIsLessThan5Characters(): void
    {
        $message = $this->getEntityContact()->setMessage(message: ('!a%'));
        self::assertLessThan(expected: 5, actual: strlen($message->getMessage()));

        $this->assertValidationErrorsCount($message, count: 1);
    }

    public function testMessageIsEmpty(): void
    {
        $message = $this->getEntityContact()->setMessage(message: (''));
        self::assertEmpty(actual: $message->getMessage());

        $this->assertValidationErrorsCount($message, count: 2);
    }
}
