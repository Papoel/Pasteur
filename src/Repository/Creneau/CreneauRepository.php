<?php

namespace App\Repository\Creneau;

use App\Entity\Creneau\Creneau;
use App\Entity\Event\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Creneau>
 *
 * @method Creneau|null find($id, $lockMode = null, $lockVersion = null)
 * @method Creneau|null findOneBy(array $criteria, array $orderBy = null)
 * @method Creneau[]    findAll()
 * @method Creneau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreneauRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Creneau::class);
    }

    public function save(Creneau $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Creneau $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array mixed
     */
    public function findByEvent(Event $event): array
    {
        return $this->createQueryBuilder(alias: 'c')
            ->innerJoin(join: 'c.event', alias: 'e')
            ->andWhere('e.slug = :event')
            ->setParameter(key: 'event', value: $event->getSlug())
            ->getQuery()
            ->getResult();
    }
}
