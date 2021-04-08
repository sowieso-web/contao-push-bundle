<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Tests\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ContainerBuilder;
use Contao\NewsBundle\ContaoNewsBundle;
use Dreibein\ContaoPushBundle\ContaoManager\Plugin;
use Dreibein\ContaoPushBundle\ContaoPushBundle;
use Minishlink\Bundle\WebPushBundle\MinishlinkWebPushBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class PluginTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testRegisterContainerConfiguration(): void
    {
        $loader = $this->createMock(LoaderInterface::class);

        $loader
            ->expects(self::once())
            ->method('load')
            ->with(self::stringContains('config/services.yml'))
        ;

        $plugin = new Plugin();

        $plugin->registerContainerConfiguration($loader, []);
    }

    public function testGetBundles(): void
    {
        $parser = $this->createMock(ParserInterface::class);

        $plugin = new Plugin();

        /** @var BundleConfig[] $bundles */
        $bundles = $plugin->getBundles($parser);

        self::assertCount(2, $bundles);

        self::assertEquals(MinishlinkWebPushBundle::class, $bundles[0]->getName());
        self::assertSame([], $bundles[0]->getLoadAfter());

        self::assertEquals(ContaoPushBundle::class, $bundles[1]->getName());
        self::assertSame(
            [
                ContaoCoreBundle::class,
                ContaoNewsBundle::class,
                MinishlinkWebPushBundle::class,
            ],
            $bundles[1]->getLoadAfter()
        );
    }

    /**
     * @throws \Exception
     */
    public function testGetRouteCollection(): void
    {
        $resolver = $this->createMock(LoaderResolverInterface::class);
        $loader = $this->createMock(LoaderInterface::class);
        $kernel = $this->createMock(KernelInterface::class);

        $resolver
            ->expects(self::once())
            ->method('resolve')
            ->with(self::stringContains('config/routing.yml'))
            ->willReturn($loader)
        ;

        $loader
            ->expects(self::once())
            ->method('load')
            ->with(self::stringContains('config/routing.yml'))
        ;

        $plugin = new Plugin();

        $plugin->getRouteCollection($resolver, $kernel);
    }

    public function testGetExtensionConfigAddsTranslations(): void
    {
        $plugin = new Plugin();
        $container = $this->createMock(ContainerBuilder::class);

        $extensionConfigs = [
            [
                'translator' => [
                    'paths' => [],
                ],
            ],
        ];

        $modifiedConfig = $plugin->getExtensionConfig('framework', $extensionConfigs, $container);

        self::assertCount(1, $modifiedConfig[0]['translator']['paths']);
    }

    public function testGetExtensionConfigAddsDoctrineMapping(): void
    {
        $plugin = new Plugin();
        $container = $this->createMock(ContainerBuilder::class);

        $extensionConfigs = [
            [
                'orm' => [
                    'mappings' => [],
                ],
            ],
        ];

        $modifiedConfig = $plugin->getExtensionConfig('doctrine', $extensionConfigs, $container);

        self::assertCount(1, $modifiedConfig[0]['orm']['mappings']);
        self::assertArrayHasKey('ContaoPushBundle', $modifiedConfig[0]['orm']['mappings']);
    }
}
