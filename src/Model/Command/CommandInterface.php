<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Command;

interface CommandInterface
{
    public function json(): string;
    public function getChannels(): iterable;
}
