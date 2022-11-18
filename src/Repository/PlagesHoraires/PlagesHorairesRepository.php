<?php

namespace App\Repository\PlagesHoraires;

use App\Entity\PlagesHoraires\PlagesHoraires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlagesHoraires>
 *
 * @method PlagesHoraires|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlagesHoraires|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlagesHoraires[]    findAll()
 * @method PlagesHoraires[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlagesHorairesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlagesHoraires::class);
    }

    public function save(PlagesHoraires $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlagesHoraires $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TimeSlot[] Returns an array of TimeSlot objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TimeSlot
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
