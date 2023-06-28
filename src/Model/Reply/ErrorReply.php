<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Reply;

class ErrorReply implements ReplyInterface
{
    private function __construct(
        private int $id,
        private int $code = 0,
        private string $message = '',
    ) {
    }

    public static function build(string $json): static
    {
        $data = json_decode($json, true);
        $payload = $data['error'];

        return new static(
            (int) $data['id'],
            $payload['code'] ?? 0,
            $payload['message'] ?? 'unknown',
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
