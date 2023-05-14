<?php

namespace App\Repository\Product;

use App\Entity\Product\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findUpComing(): array
    {
        $qb = $this->createQueryBuilder(alias: 'p')
            ->andWhere('p.startsAt < :now')
            ->andWhere('p.finishAt > :now')
            ->andWhere('p.published = true')
            ->setParameter(key: ':now', value: new \DateTime())
            ->orderBy(sort: 'p.startsAt', order: 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function countPublishedProducts(): int
    {
        $qb = $this->createQueryBuilder(alias: 'p')
            ->select(select: 'COUNT(p.id)')
            ->andWhere('p.published = true');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
