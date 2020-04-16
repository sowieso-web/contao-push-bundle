<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Werbeagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Tests\DataContainer;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use PHPUnit\Framework\TestCase;
use Dreibein\ContaoPushBundle\DataContainer\News;
use Dreibein\ContaoPushBundle\Push\PushManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class NewsTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|PushManager
     */
    private $manager;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|RequestStack
     */
    private $requestStack;
    /**
     * @var ContaoFramework|\PHPUnit\Framework\MockObject\MockObject
     */
    private $framework;

    protected function setUp(): void
    {
        $this->manager = $this->createMock(PushManager::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->framework = $this->createMock(ContaoFramework::class);
    }

    public function testOnLoadDoesNothingWithoutSendPushQueryParameter(): void
    {
        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn(new Request())
        ;

        $this->framework
            ->expects($this->never())
            ->method('initialize')
        ;

        $news = new News($this->manager, $this->requestStack, $this->framework);

        $dc = new \stdClass();
        $dc->id = 1;

        $news->onLoad($dc);
    }

    public function testOnLoadTriggersPushNotification(): void
    {
        $request = new Request(['sendPush' => 1]);

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;

        $this->framework
            ->expects($this->once())
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
            ->expects($this->exactly(2))
            ->method('getAdapter')
            ->willReturn($controllerMock, $newsModelMock)
        ;

        $this->manager
            ->expects($this->once())
            ->method('sendNotification')
        ;

        $news = new News($this->manager, $this->requestStack, $this->framework);

        $dc = new \stdClass();
        $dc->id = 1;

        $news->onLoad($dc);
    }
}
