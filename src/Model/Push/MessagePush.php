<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Push;

/**
 * Push may be sent when server sends asynchronous message to a client
 */
class MessagePush  implements PushInterface
{
    public static function build(string $json): static
    {
        return new self;
    }
}
