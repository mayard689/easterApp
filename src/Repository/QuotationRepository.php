<?php

namespace App\Repository;

use App\Entity\Quotation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quotation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quotation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quotation[]    findAll()
 * @method Quotation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quotation::class);
    }
}
