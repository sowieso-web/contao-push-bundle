<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Tests\DataContainer;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Dreibein\ContaoPushBundle\DataContainer\News;
use Dreibein\ContaoPushBundle\Push\PushManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class NewsTest extends TestCase
{
    /**
     * @var MockObject|PushManager
     */
    private $manager;
    /**
     * @var MockObject|RequestStack
     */
    private $requestStack;
    /**
     * @var ContaoFramework|MockObject
     */
    private $framework;

    protected function setUp(): void
    {
        $this->manager = $this->createMock(PushManager::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->framework = $this->createMock(ContaoFramework::class);
    }

    /**
     * @throws \ErrorException
     * @throws \JsonException
     */
    public function testOnLoadDoesNothingWithoutSendPushQueryParameter(): void
    {
        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn(new Request())
        ;

        $this->framework
            ->expects(self::never())
            ->method('initialize')
        ;

        $news = new News($this->manager, $this->requestStack, $this->framework);

        $dc = new \stdClass();
        $dc->id = 1;

        $news->onLoad($dc);
    }

    /**
     * @throws \ErrorException
     * @throws \JsonException
     */
    public function testOnLoadTriggersPushNotification(): void
    {
        $request = new Request(['sendPush' => 1]);

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;

        $this->framework
            ->expects(self::once())
            ->method('initialize')
        ;

        $controllerMock = $this
            ->getMockBuilder(Adapter::class)
            ->disableOriginalConstructor()
            ->addMethods(['replaceInsertTags'])
            ->getMock()
        ;

        $newsModelMock = $this
            ->getMockBuilder(Adapter::class)
            ->disableOriginalConstructor()
            ->addMethods(['findByPk'])
            ->getMock()
        ;

        $newsMock = new \stdClass();
        $newsMock->headline = 'Headline';
        $newsMock->subheadline = 'Subheadline';

        $newsModelMock
            ->method('findByPk')
            ->willReturn($newsMock)
        ;

        $this->framework
            ->expects(self::exactly(2))
            ->method('getAdapter')
            ->willReturn($controllerMock, $newsModelMock)
        ;

        $this->manager
            ->expects(self::once())
            ->method('sendNotification')
        ;

        $news = new News($this->manager, $this->requestStack, $this->framework);

        $dc = new \stdClass();
        $dc->id = 1;

        $news->onLoad($dc);
    }
}
