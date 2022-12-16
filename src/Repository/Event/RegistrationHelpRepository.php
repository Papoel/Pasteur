<?php

namespace App\Repository\Event;

use App\Entity\Event\Event;
use App\Entity\Event\Registration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Registration>
 *
 * @method Registration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Registration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Registration[]    findAll()
 * @method Registration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Registration::class);
    }

    public function save(Registration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Registration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // SELECT DISTINCT `activity` FROM `registration` WHERE `email` = 'pascal.briffard@gmail.com' AND `event_id` = 5
    public function findActivitiesByEventAndEmail(Event $event, string $email, string $activity)
    {
        return $this->createQueryBuilder(alias: 'r')
            ->select(select: 'DISTINCT r.activity')
            ->andWhere('r.email = :email')
            ->andWhere('r.event = :event')
            ->andWhere('r.activity = :activity')
            ->setParameter(key: 'email', value: $email)
            ->setParameter(key: 'event', value: $event->getId())
            ->setParameter(key: 'activity', value: $activity)
            ->getQuery()
            ->getResult();
    }
}
