<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Dreibein\ContaoPushBundle\Entity\Push;
use Symfony\Bridge\Doctrine\ManagerRegistry;

/**
 * @method Push|null find($id, $lockMode = null, $lockVersion = null)
 * @method Push|null findOneBy(array $criteria, array $orderBy = null)
 * @method Push[]    findAll()
 * @method Push[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PushRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Push::class);
    }
}
