<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Security;

use Akuma\Centrifugo\Model\CentrifugoUserInterface;
use Akuma\Centrifugo\Token\JwtPayload;
use Akuma\Centrifugo\Token\JwtPayloadForPrivateChannel;
use Fresh\DateTime\DateTimeHelper;

class CredentialsGenerator
{
    public function __construct(
        private JwtGenerator $jwtGenerator,
        private ?DateTimeHelper $dateTimeHelper = null,
        private ?int $centrifugoJwtTtl = null
    ) {
        $this->jwtGenerator = $jwtGenerator;
        $this->dateTimeHelper = $dateTimeHelper ?? new DateTimeHelper();
        $this->centrifugoJwtTtl = $centrifugoJwtTtl;
    }

    /**
     * @throws \JsonException
     */
    public function generateJwtTokenForUser(
        CentrifugoUserInterface $user,
        ?string $base64info = null,
        array $channels = []
    ): string {
        $jwtPayload = new JwtPayload(
            $user->getCentrifugoSubject(),
            $user->getCentrifugoUserInfo(),
            $this->getExpirationTime(),
            $base64info,
            $channels
        );

        return $this->jwtGenerator->generateToken($jwtPayload);
    }

    /**
     * @throws \JsonException
     */
    public function generateJwtTokenForAnonymous(?string $base64info = null, array $channels = []): string
    {
        $jwtPayload = new JwtPayload(
            '',
            [],
            $this->getExpirationTime(),
            $base64info,
            $channels
        );

        return $this->jwtGenerator->generateToken($jwtPayload);
    }

    /**
     * @throws \JsonException
     */
    public function generateJwtTokenForPrivateChannel(
        string $client,
        string $channel,
        ?string $base64info = null,
        ?bool $eto = null
    ): string {
        $jwtPayload = new JwtPayloadForPrivateChannel(
            $client,
            $channel,
            [],
            $this->getExpirationTime(),
            $base64info,
            $eto
        );

        return $this->jwtGenerator->generateToken($jwtPayload);
    }

    /**
     * @return int|null
     */
    private function getExpirationTime(): ?int
    {
        $expireAt = null;

        if (null !== $this->centrifugoJwtTtl) {
            $now = $this->dateTimeHelper->getCurrentDatetime();
            $now->add(new \DateInterval("PT{$this->centrifugoJwtTtl}S"));
            $expireAt = $now->getTimestamp();
        }

        return $expireAt;
    }
}
