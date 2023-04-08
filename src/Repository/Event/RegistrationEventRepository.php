<?php

namespace App\Repository\Event;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RegistrationEvent>
 *
 * @method RegistrationEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegistrationEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegistrationEvent[]    findAll()
 * @method RegistrationEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistrationEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistrationEvent::class);
    }

    public function save(RegistrationEvent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RegistrationEvent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Count the number of children registered for one registration event
    public function countChildren(RegistrationEvent $registrationEvent): int
    {
        $qb = $this->createQueryBuilder(alias: 'registrationEvents')
            ->select(select: 'COUNT(registrationEvents.id)')
            ->andWhere('registrationEvents.registrationEvent = :registrationEvent')
            ->setParameter(key: ':registrationEvent', value: $registrationEvent->getId());

        return $qb->getQuery()->getSingleScalarResult();
    }

    // Found the registration event by the slug
    public function findEventBySlug(string $slug): ?RegistrationEvent
    {
        return $this->createQueryBuilder(alias: 'registrationEvents')
            ->andWhere('registrationEvents.slug = :slug')
            ->setParameter(key: ':slug', value: $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
