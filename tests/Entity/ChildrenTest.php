<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Event\Children;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChildrenTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    public function getEntityChildren(): Children
    {
        $children = new Children();
        $children->setFirstname(firstname: 'Enfant');
        $children->setLastname(lastname: 'Numéro 2');
        $children->setClassroom(classroom: 'CM1');

        return $children;
    }

    public function assertValidationErrorsCount(Children $entity, int $count): void
    {
        $validator = static::getContainer()->get(ValidatorInterface::class);
        $violations = $validator->validate($entity);

        $messages = [];
        foreach ($violations as $violation) {
            $messages[] =
                'Erreur sur la propriété '
                . ucfirst($violation->getPropertyPath())
                . ' => ' .
                $violation->getMessage()
            ;
        }

        $this->assertCount(
            expectedCount: $count,
            haystack: $violations,
            message: implode(separator: PHP_EOL, array: $messages)
        );
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get(id: 'doctrine')
            ->getManager();
    }
    /** @test */
    public function EntityChildrenIsValid(): void
    {
        $this->assertValidationErrorsCount($this->getEntityChildren(), count: 0);
    }

    /** @test */
    public function FirstnameIsEmpty(): void
    {
        $childrenFirstname = $this->getEntityChildren()->setFirstname(firstname: '');
        self::assertEmpty(
            actual: $childrenFirstname->getFirstname(),
            message: 'Une valeur vide est attendue, or Name = "' . $childrenFirstname->getFirstname() . '"',
        );

        $this->assertValidationErrorsCount($childrenFirstname, count: 2);
    }

    /** @test */
    public function FirstnameIsGreaterThan50Characters(): void
    {
        $expected = 100;
        $childrenFirstname = $this->getEntityChildren()
            ->setFirstname(firstname: str_repeat(string: 'a', times: $expected + 1));

        $lgName = strlen($childrenFirstname->getFirstname());

        self::assertGreaterThan(
            expected: $expected,
            actual: strlen($childrenFirstname->getFirstname()),
            message:
            'Longueur calculée : '
            . $lgName .
            ' => Il est attendu + de ' . $expected . ' caractères.'
        );

        $this->assertValidationErrorsCount($childrenFirstname, count: 1);
    }

    /** @test */
    public function FirstnameIsLessThan3Characters(): void
    {
        $expected = 3;
        $childrenFirstname = $this->getEntityChildren()
            ->setFirstname(firstname: 'le');

        $lgName = strlen($childrenFirstname->getFirstname());

        self::assertLessThan(
            expected: 'le',
            actual: strlen($childrenFirstname->getFirstname()),
            message:
            'Longueur calculée : '
            . $lgName .
            ' => Il est attendu + de ' . $expected . ' caractères.'
        );

         $this->assertValidationErrorsCount($childrenFirstname, count: 1);
    }


    /** @test */
    public function LastnameIsEmpty(): void
    {
        $childrenLastname = $this->getEntityChildren()->setLastname(lastname: '');
        self::assertEmpty(
            actual: $childrenLastname->getLastname(),
            message: 'Une valeur vide est attendue, or Name = "' . $childrenLastname->getLastname() . '"',
        );

        $this->assertValidationErrorsCount($childrenLastname, count: 2);
    }

    /** @test */
    public function LastnameIsGreaterThan50Characters(): void
    {
        $expected = 100;
        $childrenLastname = $this->getEntityChildren()
            ->setLastname(lastname: str_repeat(string: 'a', times: $expected + 1));

        $lgName = strlen($childrenLastname->getLastname());

        self::assertGreaterThan(
            expected: $expected,
            actual: strlen($childrenLastname->getLastname()),
            message:
            'Longueur calculée : '
            . $lgName .
            ' => Il est attendu + de ' . $expected . ' caractères.'
        );

        $this->assertValidationErrorsCount($childrenLastname, count: 1);
    }

    /** @test */
    public function LastnameIsLessThan2Characters(): void
    {
        $expected = 2;
        $childrenLastname = $this->getEntityChildren()
            ->setLastname(lastname: 'a');

        $lgName = strlen($childrenLastname->getLastname());

        self::assertLessThan(
            expected: 'a',
            actual: strlen($childrenLastname->getLastname()),
            message: 'Longueur calculée : '
            . $lgName .
            ' => Il est attendu + de ' . $expected . ' caractères.'
        );

        $this->assertValidationErrorsCount($childrenLastname, count: 1);
    }

    /** @test */
    public function ClassroomIsGreaterThan20Characters(): void
    {
        $expected = 20;
        $childrenClassroom = $this->getEntityChildren()
            ->setClassroom(classroom: str_repeat(string: 'a', times: $expected + 1));

        $lgName = strlen($childrenClassroom->getClassroom());

        self::assertGreaterThan(
            expected: $expected,
            actual: strlen($childrenClassroom->getClassroom()),
            message:
            'Longueur calculée : '
            . $lgName .
            ' => Il est attendu + de ' . $expected . ' caractères.'
        );

        $this->assertValidationErrorsCount($childrenClassroom, count: 1);
    }

    /** @test */
    public function ClassroomIsNull(): void
    {
        $childrenClassroom = $this->getEntityChildren()->setClassroom(classroom: null);

        self::assertNull(actual: $childrenClassroom->getClassroom());

        $this->assertValidationErrorsCount($childrenClassroom, count: 1);
    }

    /** @test */
    public function ClassroomIsEmpty(): void
    {
        $childrenClassroom = $this->getEntityChildren()->setClassroom(classroom: '');

        self::assertEmpty(actual: $childrenClassroom->getClassroom());

        $this->assertValidationErrorsCount($childrenClassroom, count: 1);
    }

    /** @test */
    public function AgeCanBeNull(): void
    {
        $children = $this->getEntityChildren()->setAge(null);

        self::assertNull($children->getAge());

        $this->assertValidationErrorsCount($children, 0);
    }

    /** @test */
    public function AgeMustBeGreaterThanZero(): void
    {
        $children = $this->getEntityChildren()->setAge(age: 0);

        $this->assertValidationErrorsCount($children, count: 2);
    }

    /** @test */
    public function AgeCannotBeNegative(): void
    {
        $children = $this->getEntityChildren()->setAge(-10);

        $this->assertValidationErrorsCount($children, 2);
    }

    /** @test */
    public function AgeMustBeGreaterThanTwo(): void
    {
        $children = $this->getEntityChildren()->setAge(age: 2);

        $this->assertValidationErrorsCount($children, count: 1);
    }
}
