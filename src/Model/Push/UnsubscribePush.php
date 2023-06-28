<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Push;

/**
 * Push sent when a server unsubscribed current client from a channel
 */
class UnsubscribePush implements PushInterface
{
    public static function build(string $json): static
    {
        return new self;
    }
}
