<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Factory;

use Akuma\Centrifugo\Model\Push\PushInterface;
use Akuma\Centrifugo\Model\Reply\ReplyInterface;

class PayloadFactory implements PayloadFactoryInterface
{
    public function __construct(
        private PushFactoryInterface $pushFactory,
        private ReplyFactoryInterface $replyFactory,
    ) {
    }

    public static function default(): static
    {
        return new self(
            new PushFactory(),
            new ReplyFactory(),
        );
    }

    /**
     * @return ReplyInterface[]|PushInterface[]
     */
    public function messages(string $payload): iterable
    {
        yield from $this->replies($payload);
        yield from $this->pushes($payload);
    }

    /**
     * @return ReplyInterface[]
     */
    public function replies(string $payload): iterable
    {
        return $this->replyFactory->build($payload);
    }

    /**
     * @return PushInterface[]
     */
    public function pushes(string $payload): iterable
    {
        return $this->pushFactory->build($payload);
    }
}
