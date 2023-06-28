<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Token;

final class JwtPayload extends AbstractJwtPayload
{
    private string $subject;

    /** @var array<string> */
    private array $channels;

    /**
     * @param string $subject
     * @param array $info
     * @param int|null $expirationTime
     * @param string|null $base64info
     * @param string[] $channels
     */
    public function __construct(string $subject, array $info = [], ?int $expirationTime = null, ?string $base64info = null, array $channels = [])
    {
        $this->subject = $subject;
        $this->channels = $channels;

        parent::__construct($info, $expirationTime, $base64info);
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return array
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    /**
     * @return array
     */
    public function getPayloadData(): array
    {
        $data = [
            'sub' => $this->getSubject(),
        ];

        if (null !== $this->getExpirationTime()) {
            $data['exp'] = $this->getExpirationTime();
        }

        if (!empty($this->getInfo())) {
            $data['info'] = $this->getInfo();
        }

        if (null !== $this->getBase64Info()) {
            $data['b64info'] = $this->getBase64Info();
        }

        if (!empty($this->getChannels())) {
            $data['channels'] = $this->getChannels();
        }

        return $data;
    }
}
