<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Push;

/**
 * Push sent when someone joined (subscribed on) channel.
 */
class JoinPush  implements PushInterface
{
    public static function build(string $json): static
    {
        return new self;
    }
}
