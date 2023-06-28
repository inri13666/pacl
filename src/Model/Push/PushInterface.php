<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Push;

interface PushInterface
{
    public static function build(string $json): static;
}
