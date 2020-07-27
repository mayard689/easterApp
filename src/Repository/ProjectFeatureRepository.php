<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\ProjectFeature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
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

    public function findProjectFeatures(Project $project, string $variant) : ArrayCollection
    {
        $result = $this->createQueryBuilder('project_feature')
            ->where('project_feature.project = :project')
            ->setParameter('project', $project->getId())
            ->innerJoin('project_feature.category', 'category')
            ->innerJoin('project_feature.project', 'project')
            ->andWhere('project_feature.is'.ucfirst($variant).' = 1')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT)
        ;

        return new ArrayCollection($result);
    }
}
