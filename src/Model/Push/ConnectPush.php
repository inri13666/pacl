<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Push;

/**
 * Push can be sent in unidirectional transport case
 */
class ConnectPush implements PushInterface
{
    public static function build(string $json): static
    {
        return new self;
    }
}
