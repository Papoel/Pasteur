<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\User\User;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    public function getEntityUser(): User
    {
        $user = new User();
        /** @test */
        $user->setEmail(email: 'test@email.fr');
        $user->setRoles(roles: ['ROLE_ADMIN']);
        $user->setPassword(password: 'Password1234!');
        $user->setFirstname(firstname: 'Firstname');
        $user->setLastname(lastname: 'Lastname');
        $user->setPseudo(pseudo: 'pseudo');
        $user->setTelephone(telephone: '0625122512');
        $user->setAddress(address: '15 rue de la Liberté');
        $user->setComplementAddress(complementAddress: 'Appartement 2');
        $user->setPostalCode(postalCode: '59600');
        $user->setTown(town: 'Maubeuge');
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setUpdatedAt(new DateTimeImmutable(datetime: 'now + 1 month'));
        $user->setBirthday(birthday: new DateTime(datetime: '1085-02-20'));

        return $user;
    }
    public function assertValidationErrorsCount(User $entity, int $count): void
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
        $this->assertValidationErrorsCount($this->getEntityUser(), count: 0);
    }
    /** @test */
    public function emailIsBlank(): void
    {
        $userEmail = $this->getEntityUser()->setEmail(email: '');
        self::assertSame(expected: '', actual: $userEmail->getEmail());

        $this->assertValidationErrorsCount(entity: $userEmail, count: 1);
    }
    /** @test */
    public function emailIsValid(): void
    {
        $userEmail = $this->getEntityUser()->setEmail(email: 'papoel@email.fr');
        self::assertSame(expected: 'papoel@email.fr', actual: $userEmail->getEmail());

        $this->assertValidationErrorsCount($userEmail, count: 0);
    }
    /** @test */
    public function emailIsInvalid(): void
    {
        $userEmail = $this->getEntityUser()->setEmail(email: 'papoel.email.fr');
        self::assertSame(expected: 'papoel.email.fr', actual: $userEmail->getEmail());

        $this->assertValidationErrorsCount($userEmail, count: 1);
    }
    /** @test */
    public function firstnameIsString(): void
    {
        $userFirstname = $this->getEntityUser()->setFirstname(firstname: 'Firstname');
        self::assertIsString($userFirstname->getFirstname());

        $this->assertValidationErrorsCount(entity: $userFirstname, count: 0);
    }
    /** @test */
    public function firstnameIsGreaterThan50Characters(): void
    {
        $userFirstname = $this->getEntityUser()->setFirstname(firstname: str_repeat(string: 'a', times: 51));
        self::assertSame(expected: str_repeat(string: 'a', times: 51), actual: $userFirstname->getFirstname());

        $this->assertValidationErrorsCount(entity: $userFirstname, count: 1);
    }
    /** @test */
    public function firstnameIsSmallerThan3Characters(): void
    {
        $userFirstname = $this->getEntityUser()->setFirstname(firstname: 'aa');
        self::assertSame(expected: 'aa', actual: $userFirstname->getFirstname());

        $this->assertValidationErrorsCount(entity: $userFirstname, count: 1);
    }
    /** @test */
    public function lastnameIsGreaterThan50Characters(): void
    {
        $userLastname = $this->getEntityUser()->setLastname(lastname: str_repeat(string: 'a', times: 51));
        self::assertSame(expected: str_repeat(string: 'a', times: 51), actual: $userLastname->getLastname());

        $this->assertValidationErrorsCount(entity: $userLastname, count: 1);
    }
    /** @test */
    public function lastnameIsSmallerThan3Characters(): void
    {
        $userLastname = $this->getEntityUser()->setLastname(lastname: 'aa');
        self::assertSame(expected: 'aa', actual: $userLastname->getLastname());

        $this->assertValidationErrorsCount(entity: $userLastname, count: 1);
    }
    /** @test */
    public function pseudoIsGreaterThan50Characters(): void
    {
        $userPseudo = $this->getEntityUser()->setPseudo(pseudo: str_repeat(string: 'a', times: 51));
        self::assertSame(expected: str_repeat(string: 'a', times: 51), actual: $userPseudo->getPseudo());

        $this->assertValidationErrorsCount(entity: $userPseudo, count: 1);
    }
    /** @test */
    public function pseudoIsSmallerThan3Characters(): void
    {
        $userPseudo = $this->getEntityUser()->setPseudo(pseudo: 'aa');
        self::assertSame(expected: 'aa', actual: $userPseudo->getPseudo());

        $this->assertValidationErrorsCount(entity: $userPseudo, count: 1);
    }
    /** @test */
    public function telephoneIsGreaterThan10Characters(): void
    {
        $userTelephone = $this->getEntityUser()->setTelephone(telephone: '01234567890');
        self::assertSame(expected: '01234567890', actual: $userTelephone->getTelephone());

        $this->assertValidationErrorsCount(entity: $userTelephone, count: 1);
    }
    /** @test */
    public function telephoneIsSmallerThan10Characters(): void
    {
        $userTelephone = $this->getEntityUser()->setTelephone(telephone: '0123456');
        self::assertSame(expected: '0123456', actual: $userTelephone->getTelephone());

        $this->assertValidationErrorsCount(entity: $userTelephone, count: 1);
    }
    /** @test */
    public function addressIsGreaterThan150Characters(): void
    {
        $userAddress = $this->getEntityUser()->setAddress(address: str_repeat(string: 'a', times: 151));
        self::assertSame(expected: str_repeat(string: 'a', times: 151), actual: $userAddress->getAddress());

        $this->assertValidationErrorsCount(entity: $userAddress, count: 1);
    }
    /** @test */
    public function complementAddressIsGreaterThan200Characters(): void
    {
        $userComplementAddress =
            $this->getEntityUser()
                ->setComplementAddress(complementAddress: str_repeat(string: 'a', times: 201));
        self::assertSame(
            expected: str_repeat(
                string: 'a',
                times: 201
            ),
            actual: $userComplementAddress->getComplementAddress()
        );

        $this->assertValidationErrorsCount(entity: $userComplementAddress, count: 1);
    }
    /** @test */
    public function complementAddressIsSmallerThan4Characters(): void
    {
        $userComplementAddress = $this->getEntityUser()->setComplementAddress(complementAddress: 'WTF');
        self::assertSame(expected: 'WTF', actual: $userComplementAddress->getComplementAddress());

        $this->assertValidationErrorsCount(entity: $userComplementAddress, count: 1);
    }
    /** @test */
    public function codePostalIsGreaterThan5Characters(): void
    {
        $postal = $this->getEntityUser()->setPostalCode('123456');
        self::assertSame(expected: '123456', actual: $postal->getPostalCode());

        $this->assertValidationErrorsCount(entity: $postal, count: 1);
    }
    /** @test */
    public function codePostalIsSmallerThan5Characters(): void
    {
        $postal = $this->getEntityUser()->setPostalCode('123');
        self::assertSame(expected: '123', actual: $postal->getPostalCode());

        $this->assertValidationErrorsCount(entity: $postal, count: 1);
    }
    /** @test */
    public function townIsGreaterThan50Characters(): void
    {
        $town = $this->getEntityUser()->setTown(town: str_repeat(string: 'a', times: 51));
        self::assertSame(expected: str_repeat(string: 'a', times: 51), actual: $town->getTown());

        $this->assertValidationErrorsCount(entity: $town, count: 1);
    }
    /** @test */
    public function townIsSmallerThan3Characters(): void
    {
        $town = $this->getEntityUser()->setTown(town: 'aa');
        self::assertSame(expected: 'aa', actual: $town->getTown());

        $this->assertValidationErrorsCount(entity: $town, count: 1);
    }
    /** @test */
    public function rolesAdminExtendUser(): void
    {
        $userRole = $this->getEntityUser()->setRoles(roles: ["ROLE_ADMIN"]);
        self::assertSame(expected: [0 => "ROLE_ADMIN", 1 => "ROLE_USER"], actual: $userRole->getRoles());

        $this->assertValidationErrorsCount(entity: $userRole, count: 0);
    }
    /** @test */
    public function rolesUserExtendNoOtherRole(): void
    {
        $userRole = $this->getEntityUser()->setRoles(roles: ["ROLE_USER"]);
        self::assertSame(expected: [0 => "ROLE_USER"], actual: $userRole->getRoles());

        $this->assertValidationErrorsCount(entity: $userRole, count: 0);
    }
    /** @test */
    public function passwordIsNotBlank(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: '');
        self::assertSame(expected: '', actual: $userPassword->getPassword());

        // Erreur 1 : Le mot de passe ne doit pas être vide
        // Erreur 2 : Le mot de passe doit contenir au moins 8 caractères
        // $this->assertValidationErrorsCount(entity: $userPassword, count: 2);
    }
    /** @test */
    public function passwordHasNoMajuscule(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: 'password1234!');
        self::assertSame(expected: 'password1234!', actual: $userPassword->getPassword());

        // Erreur 1 : Le mot de passe doit contenir au moins 1 majuscule
        // $this->assertValidationErrorsCount($userPassword, count: 1);
    }
    /** @test */
    public function passwordHasNoNumber(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: 'Password!');
        self::assertSame(expected: 'Password!', actual: $userPassword->getPassword());

        // Erreur 1 : Le mot de passe doit contenir au moins 1 chiffre
        // $this->assertValidationErrorsCount($userPassword, count: 1);
    }
    /** @test */
    public function passwordHasNoLowercase(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: 'PASSWORD1234!');
        self::assertSame(expected: 'PASSWORD1234!', actual: $userPassword->getPassword());

        // Erreur 1 : Le mot de passe doit contenir au moins 1 minuscule
        // $this->assertValidationErrorsCount($userPassword, count: 1);
    }
    /** @test */
    public function passwordIsGreaterThan50Characters(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: str_repeat(string: 'Password1234!', times: 7));
        self::assertSame(expected: str_repeat(string: 'Password1234!', times: 7), actual: $userPassword->getPassword());

        // Erreur 1 : Le mot de passe ne doit contenir plus de 80 caractères
        // $this->assertValidationErrorsCount($userPassword, count: 1);
    }
    /** @test */
    public function passwordIsSmallerThan8Characters(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: 'Pas23!');
        self::assertSame(expected: 'Pas23!', actual: $userPassword->getPassword());

        // $this->assertValidationErrorsCount($userPassword, count: 1);
    }
    /** @test */
    public function passwordIsValid(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: 'Papoel59$ForTheWin');
        self::assertSame(expected: 'Papoel59$ForTheWin', actual: $userPassword->getPassword());

        $this->assertValidationErrorsCount($userPassword, count: 0);
    }

    /** @test */
    public function isVerifiedReturnsBool(): void
    {
        $user = new User();
        self::assertIsBool($user->isVerified());
    }

    /** @test */
    public function SetIsVerifiedSetsValue(): void
    {
        $user = new User();
        $user->setIsVerified(isVerified: true);
        self::assertTrue($user->isVerified());
    }

    /** @test */
    public function getAgreedTermsAtReturnsNullableDateTimeImmutable(): void
    {
        $user = new User();
        self::assertNull($user->getAgreedTermsAt());
    }

    /** @test */
    public function setAgreedTermsAtSetsValueAndReturnInstanceOfDateTimeImmutable(): void
    {
        $user = new User();
        $agreedTermsAt = new DateTimeImmutable();

        $user->setAgreedTermsAt($agreedTermsAt);
        self::assertInstanceOf(expected: DateTimeImmutable::class, actual: $user->getAgreedTermsAt());
        self::assertEquals($agreedTermsAt, $user->getAgreedTermsAt());
    }

    /** @test */
    public function getAskDeleteAccountAtReturnsNullableDateTimeImmutable(): void
    {
        $user = new User();
        self::assertNull($user->getAskDeleteAccountAt());

        $askDeleteAccountAt = new DateTimeImmutable();
        $user->setAskDeleteAccountAt($askDeleteAccountAt);
        self::assertInstanceOf(expected: DateTimeImmutable::class, actual: $user->getAskDeleteAccountAt());
        self::assertEquals($askDeleteAccountAt, $user->getAskDeleteAccountAt());
    }

    /** @test */
    public function setAskDeleteAccountAtSetsValue(): void
    {
        $user = new User();
        $askDeleteAccountAt = new DateTimeImmutable();

        $user->setAskDeleteAccountAt($askDeleteAccountAt);
        self::assertEquals($askDeleteAccountAt, $user->getAskDeleteAccountAt());

        $user->setAskDeleteAccountAt(askDeleteAccountAt: null);
        self::assertNull($user->getAskDeleteAccountAt());
    }

    /** @test */
    public function getCreatedAtReturnsNullableDateTimeImmutable(): void
    {
        $user = new User();
        self::assertInstanceOf(expected: DateTimeImmutable::class, actual: $user->getCreatedAt());
    }

    /** @test */
    public function setCreatedAtSetsValue(): void
    {
        $user = new User();
        $createdAt = new DateTimeImmutable(datetime: '2022-01-01 00:00:00');

        $user->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $user->getCreatedAt());
    }

    /** @test */
    public function getUpdatedAtReturnsNullableDateTimeImmutable(): void
    {
        $user = new User();
        self::assertNull($user->getUpdatedAt());
    }

    /** @test */
    public function setUpdatedAtSetsValue(): void
    {
        $user = new User();
        $updatedAt = new DateTimeImmutable(datetime: '2022-01-02 00:00:00');

        $user->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $user->getUpdatedAt());
    }

    /** @test */
    public function preUpdateSetsUpdatedAtWithNowDateTimeImmutableEvenIfPreviousDateIsSet(): void
    {
        $user = new User();
        $createdAt = new DateTimeImmutable(datetime: '2022-01-01 00:00:00');
        $updatedAt = new DateTimeImmutable(datetime: '2000-01-02 00:00:00');

        $user->setCreatedAt($createdAt);
        $user->setUpdatedAt($updatedAt);

        $user->preUpdate();

        self::assertGreaterThan($updatedAt, $user->getUpdatedAt());
    }

    /** @test */
    public function getBirthdayReturnsDateTimeOrNull(): void
    {
        $user = new User();
        self::assertNull($user->getBirthday());

        $birthday = new DateTime();
        $user->setBirthday($birthday);
        self::assertInstanceOf(expected: DateTime::class, actual: $user->getBirthday());
        self::assertSame($birthday, $user->getBirthday());
    }

    /** @test */
    public function setBirthdaySetsValue(): void
    {
        $user = new User();
        $birthday = new DateTime();
        $user->setBirthday($birthday);
        self::assertSame($birthday, $user->getBirthday());
    }

    /** @test */
    public function isBirthdayReturnsTrueForToday(): void
    {
        $today = new DateTimeImmutable();
        $birthday = new DateTime(sprintf(
            '%d-%d-%d',
            $today->format('Y'),
            $today->format('m'),
            $today->format('d')
        ));

        $user = new User();
        $user->setBirthday($birthday);
        self::assertTrue($user->isBirthday());
    }

    /** @test */
    public function isBirthdayReturnsFalseForNonBirthday(): void
    {
        $user = new User();

        // Set a random birthday that is not today
        $birthday = new DateTime(datetime: '1990-01-01');
        $user->setBirthday($birthday);

        self::assertFalse($user->isBirthday());
    }

    /** @test */
    public function getAgeReturnsCorrectAge(): void
    {
        $user = new User();
        $birthday = new DateTime(datetime: '1985-02-20');
        $user->setBirthday($birthday);
        $age = $user->getAge();
        self::assertSame(expected: 38, actual: $age);
    }

    /** @test */
    public function getAgeReturnsZeroForFutureBirthday(): void
    {
        $user = new User();
        $birthday = new DateTime(datetime: 'tomorrow');
        $user->setBirthday($birthday);
        $age = $user->getAge();
        self::assertSame(expected: 0, actual: $age);
    }

    /** @test */
    public function getAgeReturnsNullIfBirthdayIsNull(): void
    {
        $user = new User();
        $user->setBirthday(birthday: null);
        self::assertNull($user->getAge());
    }

    /** @test */
    public function getPlainPassword(): void
    {
        $user = new User();
        $plainPassword = 'myPassword';
        $user->setPlainPassword($plainPassword);
        self::assertEquals($plainPassword, $user->getPlainPassword());
    }

    /** @test */
    public function setPlainPassword(): void
    {
        $user = new User();
        $plainPassword = 'myPassword';
        $user->setPlainPassword($plainPassword);
        self::assertEquals($plainPassword, $user->getPlainPassword());
    }

    /** @test */
    public function toStringMethod(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'john');
        $user->setLastname(lastname: 'doe');
        self::assertEquals(expected: 'John Doe', actual: (string)$user);

        $user = new User();
        $user->setFirstname(firstname: 'jane');
        $user->setLastname(lastname: 'smith');
        self::assertEquals(expected: 'Jane Smith', actual: (string)$user);

        // Test avec un prénom composé
        $user = new User();
        $user->setFirstname(firstname: 'jean-pierre');
        $user->setLastname(lastname: 'martin');
        self::assertEquals(expected: 'Jean-Pierre Martin', actual: (string)$user);

        // Test avec un nom composé
        $user = new User();
        $user->setFirstname(firstname: 'jean-luc');
        $user->setLastname(lastname: 'perrales de la floor');
        self::assertEquals(expected: 'Jean-Luc Perrales De La Floor', actual: (string)$user);
    }
}
