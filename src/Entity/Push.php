<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Werbeagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="SimonReitinger\ContaoPushBundle\Repository\PushRepository")
 * @ORM\Table("tl_push")
 */
class Push
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $endpoint;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $publicKey;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $authToken;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $contentEncoding;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function setEndpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    public function getAuthToken(): ?string
    {
        return $this->authToken;
    }

    public function setAuthToken(string $authToken): self
    {
        $this->authToken = $authToken;

        return $this;
    }

    public function getContentEncoding(): ?string
    {
        return $this->contentEncoding;
    }

    public function setContentEncoding(string $contentEncoding): self
    {
        $this->contentEncoding = $contentEncoding;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'endpoint' => $this->getEndpoint(),
            'publicKey' => $this->getPublicKey(),
            'authToken' => $this->getAuthToken(),
            'contentEncoding' => $this->getContentEncoding(),
        ];
    }
}
