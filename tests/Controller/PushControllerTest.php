<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Werbeagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Dreibein\ContaoPushBundle\Controller\PushController;
use Dreibein\ContaoPushBundle\Entity\Push;
use Dreibein\ContaoPushBundle\Repository\PushRepository;
use Symfony\Component\HttpFoundation\Request;

class PushControllerTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $em;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|LoggerInterface
     */
    private $logger;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|PushRepository
     */
    private $pushRepository;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->pushRepository = $this->createMock(PushRepository::class);
    }

    public function testHandleSubscriptionWithoutPayload(): void
    {
        $request = $this->createRequest('ANY', false);

        $controller = new PushController($this->em, $this->pushRepository, $this->logger);

        $response = $controller->handleSubscription($request);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function testHandleSubscriptionWithSuccessfulPost(): void
    {
        $request = $this->createRequest('POST', true);

        $this->pushRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null)
        ;

        $this->em
            ->expects($this->once())
            ->method('persist')
        ;

        $this->em
            ->expects($this->once())
            ->method('flush')
        ;

        $controller = new PushController($this->em, $this->pushRepository, $this->logger);

        $response = $controller->handleSubscription($request);

        $this->assertSame(201, $response->getStatusCode());
    }

    public function testHandleSubscriptionWithSuccessfulPut(): void
    {
        $request = $this->createRequest('PUT', true);

        $push = $this->getPushMock();

        $this->pushRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($push)
        ;

        $this->em
            ->expects($this->once())
            ->method('persist')
        ;

        $this->em
            ->expects($this->once())
            ->method('flush')
        ;

        $controller = new PushController($this->em, $this->pushRepository, $this->logger);

        $response = $controller->handleSubscription($request);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testHandleSubscriptionWithPutAndNonExistentPushEntry(): void
    {
        $request = $this->createRequest('PUT', true);

        $this->pushRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null)
        ;

        $this->em
            ->expects($this->once())
            ->method('persist')
        ;

        $this->em
            ->expects($this->once())
            ->method('flush')
        ;

        $controller = new PushController($this->em, $this->pushRepository, $this->logger);

        $response = $controller->handleSubscription($request);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testHandleSubscriptionWithDelete(): void
    {
        $request = $this->createRequest('DELETE', true);

        $push = $this->getPushMock();

        $this->pushRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($push)
        ;

        $this->em
            ->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($push))
        ;

        $controller = new PushController($this->em, $this->pushRepository, $this->logger);

        $response = $controller->handleSubscription($request);

        $this->assertSame(204, $response->getStatusCode());
    }

    public function testHandleSubscriptionWithUnallowedMethod(): void
    {
        $request = $this->createRequest('PATCH', true);

        $controller = new PushController($this->em, $this->pushRepository, $this->logger);

        $response = $controller->handleSubscription($request);

        $this->assertSame(405, $response->getStatusCode());
    }

    private function createRequest(string $method, bool $usePayload)
    {
        $push = null;
        $payload = null;
        if ($usePayload) {
            $push = $this->getPushMock();
            $payload = json_encode($push->toArray());
        }

        return Request::create('/', $method, [], [], [], [], $payload);
    }

    private function getPushMock()
    {
        return (new Push())
            ->setEndpoint('custom-endpoint')
            ->setAuthToken('custom-auth-token')
            ->setContentEncoding('aesgcm')
            ->setPublicKey('public-key')
        ;
    }
}
