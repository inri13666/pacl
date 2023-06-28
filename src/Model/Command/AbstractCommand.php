<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Model\Command;

abstract class AbstractCommand implements CommandInterface
{
    /**
     * Each Command has id field. This is an incremental uint32 field.
     * This field will be echoed in a server replies to commands
     * so client could match a certain Reply to Command sent before.
     * This is important since Websocket is an asynchronous transport
     * where server and client both send messages at any moment
     * and there is no builtin request-response matching.
     * Having id allows matching a reply with a command send before on SDK level.
     *
     * @see https://centrifugal.dev/docs/transports/client_protocol#command-reply
     */
    private static int $increment = 1;

    private ?int $id = null;

    public function __construct()
    {
        $this->id = self::$increment++;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getChannels(): iterable
    {
        return [];
    }

    abstract public function json(): string;
}
