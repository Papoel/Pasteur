<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    public function test_my_env_is_in_test(): void
    {
        $kernel = self::bootKernel();
        self::assertSame(expected: 'test', actual: $kernel->getEnvironment());
        self::assertTrue(condition: true);
    }

    public function getEntityUser(): User
    {
        $user = new User();
        $user->setEmail(email: 'email@test.fr');
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
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable(datetime: 'now + 1 month'));

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
                .ucfirst($violation->getPropertyPath()).' => '.$violation->getMessage();
        }

        $this->assertCount(expectedCount: $count, haystack: $violations, message: implode(separator:PHP_EOL, array: $messages));
    }

    public function test_entity_user_is_valid(): void
    {
        $this->assertValidationErrorsCount($this->getEntityUser(), count: 0);
    }

    public function test_constraint_error_if_email_is_blank(): void
    {
        $userEmail = $this->getEntityUser()->setEmail(email: '');
        self::assertSame(expected: '', actual: $userEmail->getEmail());

        $this->assertValidationErrorsCount(entity: $userEmail, count: 1);
    }

    public function test_firstname_is_string(): void
    {
        $userFirstname = $this->getEntityUser()->setFirstname(firstname: 'Firstname');
        self::assertIsString($userFirstname->getFirstname());

        $this->assertValidationErrorsCount(entity: $userFirstname, count: 0);
    }

    /*public function test_firstname_is_not_a_string():void
    {
        $userFirstname = $this->getEntityUser()->setFirstname(firstname: 123);
        self::assertIsString($userFirstname->getFirstname());

        $this->assertValidationErrorsCount(entity: $userFirstname, count: 0);
    }*/

    public function test_firstname_is_greater_than_50_characters(): void
    {
        $userFirstname = $this->getEntityUser()->setFirstname(firstname: str_repeat(string: 'a', times: 51));
        self::assertSame(expected: str_repeat(string: 'a', times: 51), actual: $userFirstname->getFirstname());

        $this->assertValidationErrorsCount(entity: $userFirstname, count: 1);
    }

    public function test_email_is_valid(): void
    {
        $userEmail = $this->getEntityUser()->setEmail(email: 'papoel@email.fr');
        self::assertSame(expected: 'papoel@email.fr', actual: $userEmail->getEmail());

        $this->assertValidationErrorsCount($userEmail, count: 0);
    }

    public function test_firstname_is_smaller_than_3_characters(): void
    {
        $userFirstname = $this->getEntityUser()->setFirstname(firstname: 'aa');
        self::assertSame(expected: 'aa', actual: $userFirstname->getFirstname());

        $this->assertValidationErrorsCount(entity: $userFirstname, count: 1);
    }

    public function test_lastname_is_greater_than_50_characters(): void
    {
        $userLastname = $this->getEntityUser()->setLastname(lastname: str_repeat(string: 'a', times: 51));
        self::assertSame(expected: str_repeat(string: 'a', times: 51), actual: $userLastname->getLastname());

        $this->assertValidationErrorsCount(entity: $userLastname, count: 1);
    }

    public function test_lastname_is_smaller_than_3_characters(): void
    {
        $userLastname = $this->getEntityUser()->setLastname(lastname: 'aa');
        self::assertSame(expected: 'aa', actual: $userLastname->getLastname());

        $this->assertValidationErrorsCount(entity: $userLastname, count: 1);
    }

    public function test_pseudo_is_greater_than_50_characters(): void
    {
        $userPseudo = $this->getEntityUser()->setPseudo(pseudo: str_repeat(string: 'a', times: 51));
        self::assertSame(expected: str_repeat(string: 'a', times: 51), actual: $userPseudo->getPseudo());

        $this->assertValidationErrorsCount(entity: $userPseudo, count: 1);
    }

    public function test_pseudo_is_smaller_than_3_characters(): void
    {
        $userPseudo = $this->getEntityUser()->setPseudo(pseudo: 'aa');
        self::assertSame(expected: 'aa', actual: $userPseudo->getPseudo());

        $this->assertValidationErrorsCount(entity: $userPseudo, count: 1);
    }

    public function test_telephone_is_greater_than_10_characters(): void
    {
        $userTelephone = $this->getEntityUser()->setTelephone(telephone: '01234567890');
        self::assertSame(expected: '01234567890', actual: $userTelephone->getTelephone());

        $this->assertValidationErrorsCount(entity: $userTelephone, count: 1);
    }

    public function test_telephone_is_smaller_than_10_characters(): void
    {
        $userTelephone = $this->getEntityUser()->setTelephone(telephone: '0123456');
        self::assertSame(expected: '0123456', actual: $userTelephone->getTelephone());

        $this->assertValidationErrorsCount(entity: $userTelephone, count: 1);
    }

    public function test_address_is_greater_than_150_characters(): void
    {
        $userAddress = $this->getEntityUser()->setAddress(address: str_repeat(string: 'a', times: 151));
        self::assertSame(expected: str_repeat(string: 'a', times: 151), actual: $userAddress->getAddress());

        $this->assertValidationErrorsCount(entity: $userAddress, count: 1);
    }

    public function test_complement_address_is_greater_than_200_characters(): void
    {
        $userComplementAddress = $this->getEntityUser()->setComplementAddress(complementAddress: str_repeat(string: 'a', times: 201));
        self::assertSame(expected: str_repeat(string: 'a', times: 201), actual: $userComplementAddress->getComplementAddress());

        $this->assertValidationErrorsCount(entity: $userComplementAddress, count: 1);
    }

    public function test_complement_address_is_smaller_than_5_characters(): void
    {
        $userComplementAddress = $this->getEntityUser()->setComplementAddress(complementAddress: 'cool');
        self::assertSame(expected: 'cool', actual: $userComplementAddress->getComplementAddress());

        $this->assertValidationErrorsCount(entity: $userComplementAddress, count: 1);
    }

    public function test_code_postal_is_greater_than_5_characters(): void
    {
        $postal = $this->getEntityUser()->setPostalCode('123456');
        self::assertSame(expected: '123456', actual: $postal->getPostalCode());

        $this->assertValidationErrorsCount(entity: $postal, count: 1);
    }

    public function test_code_postal_is_smaller_than_5_characters(): void
    {
        $postal = $this->getEntityUser()->setPostalCode('123');
        self::assertSame(expected: '123', actual: $postal->getPostalCode());

        $this->assertValidationErrorsCount(entity: $postal, count: 1);
    }

    public function test_town_is_greater_than_50_characters(): void
    {
        $town = $this->getEntityUser()->setTown(town: str_repeat(string: 'a', times: 51));
        self::assertSame(expected: str_repeat(string: 'a', times: 51), actual: $town->getTown());

        $this->assertValidationErrorsCount(entity: $town, count: 1);
    }

    public function test_town_is_smaller_than_3_characters(): void
    {
        $town = $this->getEntityUser()->setTown(town: 'aa');
        self::assertSame(expected: 'aa', actual: $town->getTown());

        $this->assertValidationErrorsCount(entity: $town, count: 1);
    }

    public function test_roles_admin_extend_user(): void
    {
        $userRole = $this->getEntityUser()->setRoles(roles: ["ROLE_ADMIN"]);
        self::assertSame(expected: [0 => "ROLE_ADMIN", 1 => "ROLE_USER"], actual: $userRole->getRoles());

        $this->assertValidationErrorsCount(entity: $userRole, count: 0);
    }

    public function test_roles_user_extend_no_other_role(): void
    {
        $userRole = $this->getEntityUser()->setRoles(roles: ["ROLE_USER"]);
        self::assertSame(expected: [0 => "ROLE_USER"], actual: $userRole->getRoles());

        $this->assertValidationErrorsCount(entity: $userRole, count: 0);
    }

    public function test_password_is_not_blank(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: '');
        self::assertSame(expected: '', actual: $userPassword->getPassword());

        // Erreur 1 : Le mot de passe ne doit pas être vide
        // Erreur 2 : Le mot de passe doit contenir au moins 8 caractères
        $this->assertValidationErrorsCount(entity: $userPassword, count: 2);
    }

    public function test_password_has_no_majuscule(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: 'password1234!');
        self::assertSame(expected: 'password1234!', actual: $userPassword->getPassword());

        // Erreur 1 : Le mot de passe doit contenir au moins 1 majuscule
        $this->assertValidationErrorsCount($userPassword, count: 1);
    }

    public function test_password_has_no_number(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: 'Password!');
        self::assertSame(expected: 'Password!', actual: $userPassword->getPassword());

        // Erreur 1 : Le mot de passe doit contenir au moins 1 chiffre
        $this->assertValidationErrorsCount($userPassword, count: 1);
    }

    public function test_password_has_no_special_character(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: 'Password1234');
        self::assertSame(expected: 'Password1234', actual: $userPassword->getPassword());

        // Erreur 1 : Le mot de passe doit contenir au moins 1 caractère spécial
        $this->assertValidationErrorsCount($userPassword, count: 1);
    }

    public function test_password_has_no_lowercase(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: 'PASSWORD1234!');
        self::assertSame(expected: 'PASSWORD1234!', actual: $userPassword->getPassword());

        // Erreur 1 : Le mot de passe doit contenir au moins 1 minuscule
        $this->assertValidationErrorsCount($userPassword, count: 1);
    }

    public function test_password_is_greater_than_50_characters(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: str_repeat(string: 'Password1234!', times: 7));
        self::assertSame(expected: str_repeat(string: 'Password1234!', times: 7), actual: $userPassword->getPassword());

        // Erreur 1 : Le mot de passe ne doit contenir plus de 80 caractères
        $this->assertValidationErrorsCount($userPassword, count: 1);
    }

    public function test_password_is_smaller_than_8_characters(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: 'Pas23!');
        self::assertSame(expected: 'Pas23!', actual: $userPassword->getPassword());

        $this->assertValidationErrorsCount($userPassword, count: 1);
    }

    public function test_password_is_valid(): void
    {
        $userPassword = $this->getEntityUser()->setPassword(password: 'Papoel59$ForTheWin');
        self::assertSame(expected: 'Papoel59$ForTheWin', actual: $userPassword->getPassword());

        $this->assertValidationErrorsCount($userPassword, count: 0);
    }

}
