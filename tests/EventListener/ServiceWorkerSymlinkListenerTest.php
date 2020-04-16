<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Werbeagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Tests\EventListener;

use Contao\CoreBundle\Event\GenerateSymlinksEvent;
use PHPUnit\Framework\TestCase;
use Dreibein\ContaoPushBundle\EventListener\ServiceWorkerSymlinkListener;

class ServiceWorkerSymlinkListenerTest extends TestCase
{
    public function testOnGenerateSymlinks(): void
    {
        $event = $this->createMock(GenerateSymlinksEvent::class);

        $event
            ->expects($this->once())
            ->method('addSymlink')
            ->withConsecutive(
                ['web/bundles/contaopush/sw.js', 'web/contao-push-sw.js']
            )
        ;

        $listener = new ServiceWorkerSymlinkListener();

        $listener->onGenerateSymlinks($event);
    }
}
