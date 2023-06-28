<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Factory;

use Akuma\Centrifugo\Model\Push\PushInterface;

interface PushFactoryInterface
{
    /**
     * @return PushInterface[]
     */
    public function build(string $json): iterable;
}
