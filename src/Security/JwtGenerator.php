<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Security;

use Akuma\Centrifugo\Encoder\SignatureEncoder;
use Akuma\Centrifugo\Token\JwtPayloadInterface;

class JwtGenerator
{
    public function __construct(
        private SignatureEncoder $signatureEncoder
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function generateToken(JwtPayloadInterface $payload): string
    {
        $headerPart = $this->buildHeaderPart();
        $payloadPart = $this->buildPayloadPart($payload);

        $headerPartEncoded = $this->base64EncodeUrlSafe($headerPart);
        $payloadPartEncoded = $this->base64EncodeUrlSafe($payloadPart);

        return \implode('.', [
            $headerPartEncoded,
            $payloadPartEncoded,
            $this->buildSignaturePart($headerPartEncoded, $payloadPartEncoded),
        ]);
    }

    /**
     * @throws \JsonException
     */
    private function buildHeaderPart(): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        return $this->convertArrayToJsonString($header);
    }

    /**
     * @throws \JsonException
     */
    private function buildPayloadPart(JwtPayloadInterface $payload): string
    {
        return $this->convertArrayToJsonString($payload->getPayloadData());
    }

    private function buildSignaturePart(string $headerPartDecoded, string $payloadPartDecoded): string
    {
        return $this->signatureEncoder->encode($headerPartDecoded, $payloadPartDecoded);
    }

    /**
     * @throws \JsonException
     */
    private function convertArrayToJsonString(array $array): string
    {
        return \json_encode($array, \JSON_THROW_ON_ERROR);
    }

    private function base64EncodeUrlSafe(string $string): string
    {
        return \str_replace(['+', '/', '='], ['-', '_', ''], \base64_encode($string));
    }
}
