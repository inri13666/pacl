<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Command;

class ConnectCommand extends AbstractCommand
{
    public function __construct(
        private string $token,
        private string $name = 'php',
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function json(): string
    {
        return json_encode([
            'connect' => [
                "token" => $this->token,
                "name" => $this->name,
            ],
            'id' => $this->getId(),
        ]);
    }
}
