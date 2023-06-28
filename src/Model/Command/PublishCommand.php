<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Command;

class PublishCommand extends AbstractCommand
{
    public function __construct(
        private string $channel,
        private array $data = [],
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function json(): string
    {
        return json_encode([
            'id' => $this->getId(),
            'publish' => [
                'channel' => $this->channel,
                'data' => $this->data,
            ],
        ]);
    }
}
