<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Factory;

use Akuma\Centrifugo\Model\Reply\ReplyInterface;

interface ReplyFactoryInterface
{
    /**
     * @return ReplyInterface[]
     */
    public function build(string $json): iterable;
}
