<?php

namespace App\Repository\Event;

use App\Entity\Event\Children;
use App\Entity\Event\RegistrationEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Children>
 *
 * @method Children|null find($id, $lockMode = null, $lockVersion = null)
 * @method Children|null findOneBy(array $criteria, array $orderBy = null)
 * @method Children[]    findAll()
 * @method Children[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChildrenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Children::class);
    }

    public function save(Children $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Children $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @Return array Key(EventId) => Value(Total registration).
     * Get the event id from the registration_event table.
     */
    public function countChildrenByRegistration(RegistrationEvent $registration): int
    {
        return $this->createQueryBuilder(alias: 'c')
            ->select(select: 'count(c.id)')
            ->where(predicates: 'c.registrationEvent = :registration')
            ->setParameter(key: 'registration', value: $registration)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
