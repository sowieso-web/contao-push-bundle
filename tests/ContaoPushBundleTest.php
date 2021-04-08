<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Tests;

use Dreibein\ContaoPushBundle\ContaoPushBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoPushBundleTest extends TestCase
{
    public function testInstance(): void
    {
        $bundle = new ContaoPushBundle();

        self::assertInstanceOf(Bundle::class, $bundle);
    }
}
