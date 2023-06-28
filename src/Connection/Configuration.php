<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Connection;

class Configuration
{
    public function __construct(
        private string $dsn,
        private array $subProtocols = [],
        private array $headers = [],
    ) {
    }

    public function getDsn(): string
    {
        return $this->dsn;
    }

    public function getSubProtocols(): array
    {
        return $this->subProtocols;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
