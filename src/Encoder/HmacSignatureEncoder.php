<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Encoder;

class HmacSignatureEncoder implements SignatureEncoder
{
    public function __construct(
        private string $centrifugoSecret,
        private string $algorithm = 'sha256',
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function encode(string $headerPartDecoded, string $payloadPartDecoded): string
    {
        $data = $headerPartDecoded . '.' . $payloadPartDecoded;
        $hash = \hash_hmac($this->algorithm, $data, $this->centrifugoSecret, true);

        return $this->base64EncodeUrlSafe($hash);
    }

    private function base64EncodeUrlSafe(string $string): string
    {
        return \str_replace(['+', '/', '='], ['-', '_', ''], \base64_encode($string));
    }
}
