<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\EventListener;

use Contao\CoreBundle\Event\GenerateSymlinksEvent;

final class ServiceWorkerSymlinkListener
{
    /**
     * @param GenerateSymlinksEvent $event
     */
    public function onGenerateSymlinks(GenerateSymlinksEvent $event): void
    {
        $event->addSymlink('web/bundles/contaopush/sw.js', 'web/contao-push-sw.js');
    }
}
