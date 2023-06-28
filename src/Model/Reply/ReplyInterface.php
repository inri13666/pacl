<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Reply;

interface ReplyInterface
{
    public static function build(string $json): static;
    public function getId(): int;
}
