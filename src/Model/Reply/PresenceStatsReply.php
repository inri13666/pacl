<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Reply;

class PresenceStatsReply implements ReplyInterface
{
    private function __construct(
        private int $id,
        private array $data
    ) {
    }

    public static function build(string $json): static
    {
        $data = json_decode($json, true);
        $payload = $data['presence_stats'];

        return new static(
            (int) $data['id'],
            $payload,
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
