<?php

namespace App\Repository;

use App\Entity\Feature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Feature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feature[]    findAll()
 * @method Feature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feature::class);
    }

    /**
     * @param string|null $input
     * @return int|mixed|string
     */
    public function featureLikeSearch(?string $input)
    {
        return $this->createQueryBuilder('f')
            ->select('f.id', 'f.name', 'f.isStandard', 'f.description', 'f.day', 'c.id', 'c.name categoryName')
            ->leftJoin('f.category', 'c')
            ->andWhere('f.name LIKE :input')
            ->andWhere('f.isStandard = true')
            ->setParameter('input', '%' . $input . '%')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
