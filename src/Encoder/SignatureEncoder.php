<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Encoder;

interface SignatureEncoder
{
    public function encode(string $headerPartDecoded, string $payloadPartDecoded): string;
}
