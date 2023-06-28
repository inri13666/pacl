<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Push;

/**
 * Push may be sent when a server refreshes client credentials (useful in unidirectional transports)
 */
class RefreshPush  implements PushInterface
{
    public static function build(string $json): static
    {
        return new self;
    }
}
