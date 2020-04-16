<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Werbeagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Dreibein\ContaoPushBundle\Entity\Push;

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
