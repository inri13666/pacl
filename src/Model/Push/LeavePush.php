<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Push;

/**
 * Push sent when someone left (unsubscribed from) channel.
 */
class LeavePush  implements PushInterface
{
    public static function build(string $json): static
    {
        return new self;
    }
}
