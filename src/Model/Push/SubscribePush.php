<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Push;

/**
 * Push may be sent when a server subscribes client to a channel.
 */
class SubscribePush  implements PushInterface
{
    public static function build(string $json): static
    {
        return new self;
    }
}
