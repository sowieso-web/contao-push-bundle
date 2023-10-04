<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\EventListener;

use Contao\CoreBundle\Event\GenerateSymlinksEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class ServiceWorkerSymlinkListener
{
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    public function __construct(ParameterBagInterface $bag)
    {
        $this->bag = $bag;
    }

    /**
     * @param GenerateSymlinksEvent $event
     */
    public function onGenerateSymlinks(GenerateSymlinksEvent $event): void
    {
        $webDir = $this->bag->get('contao.web_dir');
        $event->addSymlink($webDir . '/bundles/contaopush/sw.js', $webDir . '/contao-push-sw.js');
    }
}
