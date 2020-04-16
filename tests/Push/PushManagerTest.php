<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Werbeagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Tests\Push;

use Doctrine\ORM\EntityManagerInterface;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\WebPush;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Dreibein\ContaoPushBundle\Entity\Push;
use Dreibein\ContaoPushBundle\Push\PushManager;
use Dreibein\ContaoPushBundle\Repository\PushRepository;

class PushManagerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var WebPush|\PHPUnit\Framework\MockObject\MockObject
     */
    private $push;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $em;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|PushRepository
     */
    private $pushRepository;

    protected function setUp(): void
    {
        $this->push = $this->createMock(WebPush::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->pushRepository = $this->createMock(PushRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    /**
     * @dataProvider getNotificationData
     *
     * @param mixed $data
     * @param mixed $subscriptionLength
     */
    public function testSendNotification(string $title, string $body, $data, $subscriptionLength, $successful, $loggerMethod): void
    {
        $this->pushRepository
            ->method('findAll')
            ->willReturn($this->createSubscriptions($subscriptionLength))
        ;

        $this->push
            ->expects($this->exactly($subscriptionLength))
            ->method('sendNotification')
        ;

        $report = $this->createMock(MessageSentReport::class);
        $request = $this->createMock(RequestInterface::class);

        $request
            ->method('getUri')
            ->willReturn('/')
        ;

        $report
            ->expects($this->once())
            ->method('isSuccess')
            ->willReturn($successful)
        ;

        $report
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;

        $this->push
            ->expects($this->once())
            ->method('flush')
            ->willReturnCallback(function () use ($report) {
                yield $report;
                return;
            })
        ;

        $this->logger
            ->expects($this->atLeastOnce())
            ->method($loggerMethod)
        ;

        $manager = new PushManager($this->push, $this->em, $this->pushRepository, $this->logger);

        $manager->sendNotification($title, $body, $data);
    }

    public function getNotificationData()
    {
        yield 'Some subscriptions' => [
            'Title',
            'Body',
            ['url' => 'https://example.com/path/to/resource'],
            3,
            true,
            'info'
        ];

        yield 'Some subscriptions that fail' => [
            'Title',
            'Body',
            ['url' => 'https://example.com/path/to/resource'],
            2,
            false,
            'error'
        ];
    }

    public function createSubscriptions($length = 0): array
    {
        $subs = [];

        for ($i = 0; $i < $length; ++$i) {
            $subs[] = (new Push())
                ->setEndpoint('custom-endpoint' . $i)
                ->setAuthToken('custom-auth-token' . $i)
                ->setContentEncoding('aesgcm')
                ->setPublicKey('public-key' . $i)
            ;
        }

        return $subs;
    }
}
