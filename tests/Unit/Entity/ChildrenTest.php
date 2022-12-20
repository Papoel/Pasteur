<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Event\Children;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChildrenTest extends KernelTestCase
{
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

    public function testEntityChildrenIsValid(): void
    {
        $this->assertValidationErrorsCount($this->getEntityChildren(), count: 0);
    }

    public function testFirstnameIsEmpty(): void
    {
        $childrenFirstname = $this->getEntityChildren()->setFirstname(firstname: '');
        self::assertEmpty(
            actual: $childrenFirstname->getFirstname(),
            message: 'Une valeur vide est attendue, or Name = "' . $childrenFirstname->getFirstname() . '"',
        );

        $this->assertValidationErrorsCount($childrenFirstname, count: 2);
    }

    public function testFirstnameIsGreaterThan50Characters(): void
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

    public function testFirstnameIsLessThan3Characters(): void
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


    public function testLastnameIsEmpty(): void
    {
        $childrenLastname = $this->getEntityChildren()->setLastname(lastname: '');
        self::assertEmpty(
            actual: $childrenLastname->getLastname(),
            message: 'Une valeur vide est attendue, or Name = "' . $childrenLastname->getLastname() . '"',
        );

        $this->assertValidationErrorsCount($childrenLastname, count: 2);
    }

    public function testLastnameIsGreaterThan50Characters(): void
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

    public function testLastnameIsLessThan2Characters(): void
    {
        $expected = 2;
        $childrenLastname = $this->getEntityChildren()
            ->setLastname(lastname: 'a');

        $lgName = strlen($childrenLastname->getLastname());

        self::assertLessThan(
            expected: 'a' ,
            actual: strlen($childrenLastname->getLastname()) ,
            message: 'Longueur calculée : '
            . $lgName .
            ' => Il est attendu + de ' . $expected . ' caractères.'
        );

        $this->assertValidationErrorsCount($childrenLastname , count: 1);
    }

    public function testClassroomIsGreaterThan4Characters(): void
    {
        $expected = 4;
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

    public function testClassroomIsNull(): void
    {
        $childrenClassroom = $this->getEntityChildren()->setClassroom(classroom: null);

        self::assertNull(actual: $childrenClassroom->getClassroom());

        $this->assertValidationErrorsCount($childrenClassroom, count: 0);
    }

    public function testClassroomIsEmpty(): void
    {
        $childrenClassroom = $this->getEntityChildren()->setClassroom(classroom: '');

        self::assertEmpty(actual: $childrenClassroom->getClassroom());

        $this->assertValidationErrorsCount($childrenClassroom, count: 0);
    }
}
