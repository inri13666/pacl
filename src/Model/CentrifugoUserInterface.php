<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model;

interface CentrifugoUserInterface
{
    /**
     * @return string
     */
    public function getCentrifugoSubject(): string;

    /**
     * @return mixed[]
     */
    public function getCentrifugoUserInfo(): array;
}
