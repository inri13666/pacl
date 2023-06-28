<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Push;

/**
 * Push sent when something published into channel
 *
 * {"push":{"channel":"loop24","pub":{"data":{"xxx":123}}}}
 */
class PubPush  implements PushInterface
{
    public static function build(string $json): static
    {
        return new self;
    }
}
