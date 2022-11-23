<?php

namespace App\Repository\Event;

use App\Entity\Creneau\Creneau;
use App\Entity\Event\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
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

    public function findUpcoming()
    {
        $qb = $this->createQueryBuilder('events')
            ->andWhere('events.startsAt > :now')
            ->setParameter(':now', new \DateTime())
            ->orderBy('events.startsAt', 'ASC');

        return $qb->getQuery()->getResult();
    }

    // query for select creneaux from event
    public function findCreneaux(Event $event)
    {
        $qb = $this->createQueryBuilder('events')
            ->andWhere('events.slug = :event')
            ->setParameter(':event', $event->getSlug())
            ->orderBy('events.startsAt', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
