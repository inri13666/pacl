<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Reply;

class ConnectReply implements ReplyInterface
{
    private function __construct(
        private int $id,
        private string $client,
        private string $version = 'unknown',
        private array $subscriptions = [],
        private int $ping = 25,
        private bool $pong = true,
    ) {
    }

    public static function build(string $json): static
    {
        $data = json_decode($json, true);
        $payload = $data['connect'];

        return new static(
            (int) $data['id'],
            $payload['client'] ?? 'unknown',
            $payload['version'] ?? 'unknown',
            $payload['subs'] ?? [],
            $payload['ping'] ?? 25,
            $payload['pong'] ?? true,
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getClient(): string
    {
        return $this->client;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getSubscriptions(): array
    {
        return $this->subscriptions;
    }

    public function getPing(): int
    {
        return $this->ping;
    }

    public function isPong(): bool
    {
        return $this->pong;
    }
}
