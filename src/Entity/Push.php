<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Push Bundle.
 * (c) Digitalagentur Dreibein GmbH
 */

namespace Dreibein\ContaoPushBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Dreibein\ContaoPushBundle\Repository\PushRepository")
 * @ORM\Table("tl_push")
 */
class Push
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $endpoint;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $publicKey;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $authToken;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $contentEncoding;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     *
     * @return $this
     */
    public function setEndpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     *
     * @return $this
     */
    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthToken(): ?string
    {
        return $this->authToken;
    }

    /**
     * @param string $authToken
     *
     * @return $this
     */
    public function setAuthToken(string $authToken): self
    {
        $this->authToken = $authToken;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContentEncoding(): ?string
    {
        return $this->contentEncoding;
    }

    /**
     * @param string $contentEncoding
     *
     * @return $this
     */
    public function setContentEncoding(string $contentEncoding): self
    {
        $this->contentEncoding = $contentEncoding;

        return $this;
    }

    /**
     * @return null[]|string[]
     */
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
