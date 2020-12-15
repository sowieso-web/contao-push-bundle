<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Werbeagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Tests\Entity;

use Dreibein\ContaoPushBundle\Entity\Push;
use PHPUnit\Framework\TestCase;

class PushTest extends TestCase
{
    public function testGetters(): void
    {
        $push = (new Push())
            ->setEndpoint('custom-endpoint')
            ->setAuthToken('custom-auth-token')
            ->setContentEncoding('aesgcm')
            ->setPublicKey('public-key')
        ;

        $this->assertSame('custom-endpoint', $push->getEndpoint());
        $this->assertSame('custom-auth-token', $push->getAuthToken());
        $this->assertSame('aesgcm', $push->getContentEncoding());
        $this->assertSame('public-key', $push->getPublicKey());
    }

    public function testToArray(): void
    {
        $push = (new Push())
            ->setEndpoint('custom-endpoint')
            ->setAuthToken('custom-auth-token')
            ->setContentEncoding('aesgcm')
            ->setPublicKey('public-key')
        ;

        $expected = [
            'endpoint' => 'custom-endpoint',
            'authToken' => 'custom-auth-token',
            'contentEncoding' => 'aesgcm',
            'publicKey' => 'public-key',
        ];

        $this->assertEqualsCanonicalizing($expected, $push->toArray());
    }

    public function testGetId(): void
    {
        $push = new Push();

        $this->assertNull($push->getId());
    }
}
