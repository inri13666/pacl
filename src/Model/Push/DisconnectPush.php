<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Push;

/**
 * Push may be sent be a server before closing connection and contains disconnect code/reason
 */
class DisconnectPush implements PushInterface
{
    public static function build(string $json): static
    {
        return new self;
    }
}
