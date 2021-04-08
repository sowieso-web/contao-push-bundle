<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Tests\Push;

use Doctrine\ORM\EntityManagerInterface;
use Dreibein\ContaoPushBundle\Entity\Push;
use Dreibein\ContaoPushBundle\Push\PushManager;
use Dreibein\ContaoPushBundle\Repository\PushRepository;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\WebPush;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

class PushManagerTest extends TestCase
{
    /**
     * @var MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var WebPush|MockObject
     */
    private $push;

    /**
     * @var EntityManagerInterface|MockObject
     */
    private $em;

    /**
     * @var MockObject|PushRepository
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
     * @param string $title
     * @param string $body
     * @param $data
     * @param $subscriptionLength
     * @param $successful
     * @param $loggerMethod
     *
     * @throws \ErrorException
     * @throws \JsonException
     */
    public function testSendNotification(string $title, string $body, $data, $subscriptionLength, $successful, $loggerMethod): void
    {
        $this->pushRepository
            ->method('findAll')
            ->willReturn($this->createSubscriptions((int) $subscriptionLength))
        ;

        $this->push
            ->expects(self::exactly($subscriptionLength))
            ->method('sendNotification')
        ;

        $report = $this->createMock(MessageSentReport::class);
        $request = $this->createMock(RequestInterface::class);

        $request
            ->method('getUri')
            ->willReturn('/')
        ;

        $report
            ->expects(self::once())
            ->method('isSuccess')
            ->willReturn($successful)
        ;

        $report
            ->expects(self::once())
            ->method('getRequest')
            ->willReturn($request)
        ;

        $this->push
            ->expects(self::once())
            ->method('flush')
            ->willReturnCallback(function () use ($report) {
                yield $report;
            })
        ;

        $this->logger
            ->expects(self::atLeastOnce())
            ->method($loggerMethod)
        ;

        $manager = new PushManager($this->push, $this->em, $this->pushRepository, $this->logger);

        $manager->sendNotification($title, $body, $data);
    }

    public function getNotificationData(): \Generator
    {
        yield 'Some subscriptions' => [
            'Title',
            'Body',
            ['url' => 'https://example.com/path/to/resource'],
            3,
            true,
            'info',
        ];

        yield 'Some subscriptions that fail' => [
            'Title',
            'Body',
            ['url' => 'https://example.com/path/to/resource'],
            2,
            false,
            'error',
        ];
    }

    /**
     * @param int $length
     *
     * @return array
     */
    public function createSubscriptions(int $length = 0): array
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
