<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Dreibein\ContaoPushBundle\Controller\PushController;
use Dreibein\ContaoPushBundle\Entity\Push;
use Dreibein\ContaoPushBundle\Repository\PushRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class PushControllerTest extends TestCase
{
    /**
     * @var EntityManagerInterface|MockObject
     */
    private $em;

    /**
     * @var MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var MockObject|PushRepository
     */
    private $pushRepository;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->pushRepository = $this->createMock(PushRepository::class);
    }

    /**
     * @throws \ErrorException
     * @throws \JsonException
     */
    public function testHandleSubscriptionWithoutPayload(): void
    {
        $request = $this->createRequest('ANY', false);

        $controller = new PushController($this->em, $this->pushRepository, $this->logger);

        $response = $controller->handleSubscription($request);

        self::assertSame(400, $response->getStatusCode());
    }

    /**
     * @throws \ErrorException
     * @throws \JsonException
     */
    public function testHandleSubscriptionWithSuccessfulPost(): void
    {
        $request = $this->createRequest('POST', true);

        $this->pushRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn(null)
        ;

        $this->em
            ->expects(self::once())
            ->method('persist')
        ;

        $this->em
            ->expects(self::once())
            ->method('flush')
        ;

        $controller = new PushController($this->em, $this->pushRepository, $this->logger);

        $response = $controller->handleSubscription($request);

        self::assertSame(201, $response->getStatusCode());
    }

    /**
     * @throws \ErrorException
     * @throws \JsonException
     */
    public function testHandleSubscriptionWithSuccessfulPut(): void
    {
        $request = $this->createRequest('PUT', true);

        $push = $this->getPushMock();

        $this->pushRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn($push)
        ;

        $this->em
            ->expects(self::once())
            ->method('persist')
        ;

        $this->em
            ->expects(self::once())
            ->method('flush')
        ;

        $controller = new PushController($this->em, $this->pushRepository, $this->logger);

        $response = $controller->handleSubscription($request);

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @throws \ErrorException
     * @throws \JsonException
     */
    public function testHandleSubscriptionWithPutAndNonExistentPushEntry(): void
    {
        $request = $this->createRequest('PUT', true);

        $this->pushRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn(null)
        ;

        $this->em
            ->expects(self::once())
            ->method('persist')
        ;

        $this->em
            ->expects(self::once())
            ->method('flush')
        ;

        $controller = new PushController($this->em, $this->pushRepository, $this->logger);

        $response = $controller->handleSubscription($request);

        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * @throws \ErrorException
     * @throws \JsonException
     */
    public function testHandleSubscriptionWithDelete(): void
    {
        $request = $this->createRequest('DELETE', true);

        $push = $this->getPushMock();

        $this->pushRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->willReturn($push)
        ;

        $this->em
            ->expects(self::once())
            ->method('remove')
            ->with(self::equalTo($push))
        ;

        $controller = new PushController($this->em, $this->pushRepository, $this->logger);

        $response = $controller->handleSubscription($request);

        self::assertSame(204, $response->getStatusCode());
    }

    /**
     * @throws \ErrorException
     * @throws \JsonException
     */
    public function testHandleSubscriptionWithUnallowedMethod(): void
    {
        $request = $this->createRequest('PATCH', true);

        $controller = new PushController($this->em, $this->pushRepository, $this->logger);

        $response = $controller->handleSubscription($request);

        self::assertSame(405, $response->getStatusCode());
    }

    /**
     * @param string $method
     * @param bool   $usePayload
     *
     * @throws \JsonException
     *
     * @return Request
     */
    private function createRequest(string $method, bool $usePayload): Request
    {
        $push = null;
        $payload = null;
        if ($usePayload) {
            $push = $this->getPushMock();
            $payload = json_encode($push->toArray(), \JSON_THROW_ON_ERROR);
        }

        return Request::create('/', $method, [], [], [], [], $payload);
    }

    /**
     * @return Push
     */
    private function getPushMock(): Push
    {
        return (new Push())
            ->setEndpoint('custom-endpoint')
            ->setAuthToken('custom-auth-token')
            ->setContentEncoding('aesgcm')
            ->setPublicKey('public-key')
        ;
    }
}
