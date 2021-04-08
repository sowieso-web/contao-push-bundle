<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Tests\Controller\FrontendModule;

use Contao\ModuleModel;
use Contao\Template;
use Dreibein\ContaoPushBundle\Controller\FrontendModule\PushNotificationButton;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

class PushNotificationButtonTest extends TestCase
{
    public function testInvokeAddsNecessaryScripts(): void
    {
        $request = new Request();
        $model = $this->getMockBuilder(ModuleModel::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $bag = $this->createMock(ParameterBagInterface::class);

        $bag
            ->method('get')
            ->with('minishlink_web_push.auth')
            ->willReturn([
                'VAPID' => [
                    'publicKey' => 'generated-public-key-from-config.yml',
                ],
            ])
        ;

        $template = $this->createMock(Template::class);

        $controller = new PushNotificationButton($bag);

        $GLOBALS['TL_BODY'] = [];

        $controller->getResponse($template, $model, $request);

        self::assertSame("<script>const applicationServerKey = 'generated-public-key-from-config.yml';</script>", $GLOBALS['TL_BODY']['contao_push_key']);
        self::assertSame('<script src="/bundles/contaopush/main.min.js"></script>', $GLOBALS['TL_BODY']['contao_push']);
    }
}
