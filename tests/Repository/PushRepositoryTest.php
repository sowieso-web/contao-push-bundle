<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Werbeagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Dreibein\ContaoPushBundle\Repository\PushRepository;

class PushRepositoryTest extends TestCase
{
    public function test__construct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);

        $this->expectException('LogicException');

        $repository = new PushRepository($registry);
    }
}
