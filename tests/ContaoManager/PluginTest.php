<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Werbeagentur Dreibein GmbH
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
    public function testRegisterContainerConfiguration(): void
    {
        $loader = $this->createMock(LoaderInterface::class);

        $loader
            ->expects($this->once())
            ->method('load')
            ->with($this->stringContains('Resources/config/services.yml'))
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

        $this->assertCount(2, $bundles);

        $this->assertEquals(MinishlinkWebPushBundle::class, $bundles[0]->getName());
        $this->assertSame(
            [],
            $bundles[0]->getLoadAfter()
        );

        $this->assertEquals(ContaoPushBundle::class, $bundles[1]->getName());
        $this->assertSame(
            [
                ContaoCoreBundle::class,
                ContaoNewsBundle::class,
                MinishlinkWebPushBundle::class,
            ],
            $bundles[1]->getLoadAfter()
        );
    }

    public function testGetRouteCollection(): void
    {
        $resolver = $this->createMock(LoaderResolverInterface::class);
        $loader = $this->createMock(LoaderInterface::class);
        $kernel = $this->createMock(KernelInterface::class);

        $resolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->stringContains('Resources/config/routing.yml'))
            ->willReturn($loader)
        ;

        $loader
            ->expects($this->once())
            ->method('load')
            ->with($this->stringContains('Resources/config/routing.yml'))
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

        $this->assertCount(1, $modifiedConfig[0]['translator']['paths']);
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

        $this->assertCount(1, $modifiedConfig[0]['orm']['mappings']);
        $this->assertArrayHasKey('ContaoPushBundle', $modifiedConfig[0]['orm']['mappings']);
    }
}
