<?php

declare(strict_types=1);

namespace Akuma\Centrifugo;

use Akuma\Centrifugo\Client\WebSocketClient;
use Akuma\Centrifugo\Connection\Configuration;
use Akuma\Centrifugo\Encoder\HmacSignatureEncoder;
use Akuma\Centrifugo\Model\CentrifugoUserInterface;
use Akuma\Centrifugo\Model\Command\PublishCommand;
use Akuma\Centrifugo\Security\CredentialsGenerator;
use Akuma\Centrifugo\Security\JwtGenerator;
use Psr\Log\AbstractLogger;
use React\EventLoop\Loop;

require_once './../vendor/autoload.php';

$signatureEncoder = new HmacSignatureEncoder('akuma-cent-token-hmac-secret-key');

$jwtGenerator = new JwtGenerator($signatureEncoder);
$credentialsGenerator = new CredentialsGenerator($jwtGenerator);

$config = new Configuration(
    'ws://localhost:8569/connection/websocket',
    headers: [
        'Origin' => 'http://pacl.akuma.local',
    ]
);
$client = new WebSocketClient($config);

$user = new class implements CentrifugoUserInterface {
    public function getCentrifugoSubject(): string
    {
        return 'test-user-' . time();
    }

    public function getCentrifugoUserInfo(): array
    {
        return [];
    }
};

$logger = new class extends AbstractLogger {
    /**
     * {@inheritDoc}
     */
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        echo match ($level) {
                default => (string) $message
            } . PHP_EOL;
    }
};

$token = $credentialsGenerator->generateJwtTokenForUser($user, channels: ['pacl-example']);

$client->setLogger($logger);
$client->connect($token);

// Sleep 1 second to ensure that connection is established and client info provided
Loop::get()->addTimer(1, function () use ($client) {
    if (!$client->isAuthenticated()) {
        throw new \RuntimeException('Unable to connect to Centrifuge');
    } else {
        Loop::get()->addPeriodicTimer(2, function () use ($client) {
            if ($client->getClientInfo()) {
                $client->command(new PublishCommand(
                    'pacl-example',
                    [
                        'time' => (new \DateTimeImmutable())->format(DATE_ATOM),
                    ]
                ));
            }
        });
    }
});
