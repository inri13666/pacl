<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Token;

interface JwtPayloadInterface
{
    /**
     * @return array
     */
    public function getPayloadData(): array;
}
