<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Feature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
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
            ->select('f.name', 'f.description', 'f.day')
            ->distinct()
            ->where('f.name LIKE :input')
            ->setParameter('input', '%' . $input . '%')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
