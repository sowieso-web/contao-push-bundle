<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use Contao\ManagerPlugin\Config\ContainerBuilder;
use Contao\ManagerPlugin\Config\ExtensionPluginInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Contao\NewsBundle\ContaoNewsBundle;
use Dreibein\ContaoPushBundle\ContaoPushBundle;
use Minishlink\Bundle\WebPushBundle\MinishlinkWebPushBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

class Plugin implements BundlePluginInterface, ConfigPluginInterface, RoutingPluginInterface, ExtensionPluginInterface
{
    /**
     * @param ParserInterface $parser
     *
     * @return array
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(MinishlinkWebPushBundle::class),
            BundleConfig::create(ContaoPushBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                    ContaoNewsBundle::class,
                    MinishlinkWebPushBundle::class,
                ]),
        ];
    }

    /**
     * @param LoaderInterface $loader
     * @param array           $managerConfig
     *
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader, array $managerConfig): void
    {
        $loader->load(__DIR__ . '/../../config/services.yaml');
    }

    /**
     * @param LoaderResolverInterface $resolver
     * @param KernelInterface         $kernel
     *
     * @throws \Exception
     *
     * @return RouteCollection|null
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel): ?RouteCollection
    {
        $file = __DIR__ . '/../../config/routing.yaml';

        return $resolver
            ->resolve($file)
            ->load($file)
        ;
    }

    /**
     * @param string           $extensionName
     * @param array            $extensionConfigs
     * @param ContainerBuilder $container
     *
     * @return array
     */
    public function getExtensionConfig($extensionName, array $extensionConfigs, ContainerBuilder $container): array
    {
        // translations
        if ('framework' === $extensionName) {
            $extensionConfigs[0]['translator']['paths'][] = '%kernel.project_dir%/vendor/dreibein/contao-push-bundle/translations';
        }

        // doctrine mapping
        if ('doctrine' === $extensionName) {
            $extensionConfigs[0]['orm']['mappings']['ContaoPushBundle'] = [
                'type' => 'annotation',
            ];
        }

        return $extensionConfigs;
    }
}
