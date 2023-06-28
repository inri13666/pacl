<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Client;

use Akuma\Centrifugo\Connection\Configuration;
use Akuma\Centrifugo\Connection\Connector;
use Akuma\Centrifugo\Connection\WebSocketConnection;
use Akuma\Centrifugo\Factory\PayloadFactory;
use Akuma\Centrifugo\Factory\PayloadFactoryInterface;
use Akuma\Centrifugo\Model\Command\CommandInterface;
use Akuma\Centrifugo\Model\Command\ConnectCommand;
use Akuma\Centrifugo\Model\Reply\ConnectReply;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Ratchet\RFC6455\Messaging\FrameInterface;
use Ratchet\RFC6455\Messaging\Message;
use React\Promise\PromiseInterface;

class WebSocketClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private Connector $connector;

    private PromiseInterface $promise;

    private ?WebSocketConnection $conn = null;

    private ?ConnectCommand $authRequest = null;

    private ?ConnectReply $clientInfo = null;

    private PayloadFactoryInterface $payloadFactory;

    public function __construct(
        private Configuration $configuration
    ) {
        $this->connector = new Connector();
        $this->logger = new NullLogger();
        $this->payloadFactory = PayloadFactory::default();
    }

    public function connect(string $token): void
    {
        $this->promise = $this->connector->connect(
            $this->configuration->getDsn(),
            $this->configuration->getSubProtocols(),
            $this->configuration->getHeaders(),
        );
        $this->promise->then(
            function (WebSocketConnection $conn) use ($token) {
                $this->conn = $conn;
                $this->configureLogger($conn);
                $this->configureRepliesEmitter($conn);
                $conn->on(ConnectReply::class, function (ConnectReply $reply) {
                    if (!$this->clientInfo) {
                        $this->clientInfo = $reply;
                    }
                });

                // Authenticate
                $this->authRequest = new ConnectCommand(
                    $token,
                );
                $this->command($this->authRequest);
            },
            fn ($e) => $this->logger->critical(sprintf('Could not connect: %s', $e->getMessage()))
        );
    }

    public function getClientInfo(): ?ConnectReply
    {
        return $this->clientInfo;
    }

    public function isAuthenticated(): bool
    {
        return $this->clientInfo && $this->authRequest?->getId() === $this->clientInfo?->getId();
    }

    public function command(CommandInterface $command): bool
    {
        if ($this->conn) {
            return $this->conn->send($command->json());
        }

        return false;
    }

    private function configureLogger(WebSocketConnection $conn): void
    {
        $conn->setLogger($this->logger);
        /**
         * TODO: add additional top level events to logger
         * @event message
         * @event ping
         * @event pong
         * @event close
         * @event error
         */
        $conn->on('message', function (Message $msg) {
            $this->logger->debug(sprintf('incoming message: %s', $msg->getPayload()));
        });
    }

    private function configureRepliesEmitter(WebSocketConnection $conn): void
    {
        $conn->on('message', function (Message $msg) use ($conn) {
            /** @var FrameInterface $frame */
            foreach ($msg as $frame) {
                $messages = $this->payloadFactory->messages($frame->getPayload());
                foreach ($messages as $message) {
                    $conn->emit(get_class($message), [$message, $this]);
                }
            }
        });
    }
}
