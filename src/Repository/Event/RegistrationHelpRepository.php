<?php

namespace App\Repository\Event;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationHelp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RegistrationHelp>
 *
 * @method RegistrationHelp|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegistrationHelp|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegistrationHelp[]    findAll()
 * @method RegistrationHelp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistrationHelpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistrationHelp::class);
    }

    public function save(RegistrationHelp $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RegistrationHelp $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

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
