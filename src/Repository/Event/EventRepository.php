<?php

namespace App\Repository\Event;

use App\Entity\Event\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function save(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findUpcoming(): array
    {
        $qb = $this->createQueryBuilder(alias: 'events')
            ->andWhere('events.startsAt > :now')
            ->andWhere('events.published = true')
            ->setParameter(key: ':now', value: new \DateTime())
            ->orderBy(sort: 'events.startsAt', order: 'ASC');

        return $qb->getQuery()->getResult();
    }

    // query for select creneaux from event
    public function findCreneaux(Event $event): Event
    {
        $qb = $this->createQueryBuilder(alias: 'events')
            ->andWhere('events.slug = :event')
            ->setParameter(key: ':event', value: $event->getSlug())
            ->orderBy(sort: 'events.startsAt', order: 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function countNotPastEvents(): int
    {
        $query = $this->createQueryBuilder(alias: 'events')
            ->select(select: 'COUNT(events)')
            ->andWhere('events.startsAt > :now')
            ->setParameter(key: ':now', value: new \DateTime());

        return $query->getQuery()->getSingleScalarResult();
    }

    public function countPublishedEvents(): int
    {
        $query = $this->createQueryBuilder(alias: 'e')
            ->select(select: 'count(e.id)')
            ->andWhere('e.published = true')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function findAllPublishedEvents(): array
    {
        $query = $this->createQueryBuilder(alias: 'e')
            ->andWhere('e.published = true')
            ->getQuery();

        return $query->getResult();
    }

    public function countEventsWithCapacityZero(): int
    {
        $query = $this->createQueryBuilder(alias: 'e')
            ->select(select: 'count(e.id)')
            ->andWhere('e.capacity = 0')
            ->getQuery();

        return $query->getSingleScalarResult();
    }
}
