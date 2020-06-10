<?php

namespace App\Repository;

use App\Entity\ProjectFeature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectFeature|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectFeature|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectFeature[]    findAll()
 * @method ProjectFeature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectFeatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectFeature::class);
    }
}
