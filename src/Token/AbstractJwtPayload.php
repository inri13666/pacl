<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Token;

abstract class AbstractJwtPayload implements JwtPayloadInterface
{
    private ?int $expirationTime;
    private array $info;
    private ?string $base64info; // phpcs:ignore

    /**
     * @param array $info
     * @param int|null $expirationTime
     * @param string|null $base64info
     */
    public function __construct(array $info = [], ?int $expirationTime = null, ?string $base64info = null)
    {
        $this->info = $info;
        $this->expirationTime = $expirationTime;
        $this->base64info = $base64info;
    }

    /**
     * @return int|null
     */
    public function getExpirationTime(): ?int
    {
        return $this->expirationTime;
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * @return string|null
     */
    public function getBase64Info(): ?string
    {
        return $this->base64info;
    }
}
