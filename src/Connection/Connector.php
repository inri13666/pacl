<?php

namespace Akuma\Centrifugo\Connection;

use GuzzleHttp\Psr7 as gPsr;
use Psr\Http\Message\RequestInterface;
use Ratchet\RFC6455\Handshake\ClientNegotiator;
use Ratchet\RFC6455\Messaging\Message;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use React\Promise\RejectedPromise;
use React\Socket\ConnectionInterface;
use React\Socket\ConnectorInterface;

class Connector
{
    public function __construct(
        private ?LoopInterface $loop = null,
        private ?ConnectorInterface $connector = null,
        protected ?ClientNegotiator $clientNegotiator = null,
    ) {
        $this->loop = $loop ?: Loop::get();

        if (null === $connector) {
            $connector = new \React\Socket\Connector([
                'timeout' => 20,
            ], $this->loop);
        }

        $this->connector = $connector;
        $this->clientNegotiator = $clientNegotiator ?? new ClientNegotiator;
    }

    public function connect($url, array $subProtocols = [], array $headers = []): PromiseInterface
    {
        try {
            $request = $this->generateRequest($url, $subProtocols, $headers);
            $uri = $request->getUri();
        } catch (\Exception $e) {
            return new RejectedPromise($e);
        }
        $secure = 'wss' === substr($url, 0, 3);
        $connector = $this->connector;

        $port = $uri->getPort() ?: ($secure ? 443 : 80);

        $scheme = $secure ? 'tls' : 'tcp';

        $uriString = $scheme . '://' . $uri->getHost() . ':' . $port;

        $connecting = $connector->connect($uriString);

        $futureWsConn = new Deferred(function ($_, $reject) use ($url, $connecting) {
            $reject(new \RuntimeException(
                'Connection to ' . $url . ' cancelled during handshake'
            ));

            // either close active connection or cancel pending connection attempt
            $connecting->then(function (ConnectionInterface $connection) {
                $connection->close();
            });
            $connecting->cancel();
        });

        $connecting->then(function (ConnectionInterface $conn) use ($request, $subProtocols, $futureWsConn) {
            $earlyClose = function () use ($futureWsConn) {
                $futureWsConn->reject(new \RuntimeException('Connection closed before handshake'));
            };

            $stream = $conn;

            $stream->on('close', $earlyClose);
            $futureWsConn->promise()->then(function () use ($stream, $earlyClose) {
                $stream->removeListener('close', $earlyClose);
            });

            $buffer = '';
            $headerParser = function ($data) use (
                $stream,
                &$headerParser,
                &$buffer,
                $futureWsConn,
                $request,
                $subProtocols
            ) {
                $buffer .= $data;
                if (false == strpos($buffer, "\r\n\r\n")) {
                    return;
                }

                $stream->removeListener('data', $headerParser);

                $response = gPsr\Message::parseResponse($buffer);

                if (!$this->clientNegotiator->validateResponse($request, $response)) {
                    $futureWsConn->reject(new \DomainException(gPsr\Message::toString($response)));
                    $stream->close();

                    return;
                }

                $acceptedProtocol = $response->getHeader('Sec-WebSocket-Protocol');
                if ((count($subProtocols) > 0) && 1 !== count(array_intersect($subProtocols, $acceptedProtocol))) {
                    $futureWsConn->reject(new \DomainException('Server did not respond with an expected Sec-WebSocket-Protocol'));
                    $stream->close();

                    return;
                }

                $futureWsConn->resolve(new WebSocketConnection($stream, $response, $request));

                $futureWsConn->promise()->then(function (WebSocketConnection $conn) use ($stream) {
                    $stream->emit('data', [$conn->response->getBody(), $stream]);
                });

                $futureWsConn->promise()->then(function (WebSocketConnection $conn) use ($stream) {
                    $conn->on('message', function (Message $msg) use ($conn) {
                        $payload = $msg->getPayload();
                        // Handle Ping Pong
                        if ('{}' === $payload) {
                            $conn->send('{}');

                            return;
                        }
                    });
                });
            };

            $stream->on('data', $headerParser);
            $stream->write(gPsr\Message::toString($request));
        }, [$futureWsConn, 'reject']);

        return $futureWsConn->promise();
    }

    /**
     * @param string $url
     * @param array $subProtocols
     * @param array $headers
     *
     * @return \Psr\Http\Message\RequestInterface
     * @throws \InvalidArgumentException
     */
    protected function generateRequest($url, array $subProtocols, array $headers)
    {
        $uri = gPsr\Utils::uriFor($url);

        $scheme = $uri->getScheme();

        if (!in_array($scheme, ['ws', 'wss'])) {
            throw new \InvalidArgumentException(sprintf('Cannot connect to invalid URL (%s)', $url));
        }

        $uri = $uri->withScheme('wss' === $scheme ? 'HTTPS' : 'HTTP');

        $headers += ['User-Agent' => 'Akuma-Centrifugo/0.0.1'];

        $request = array_reduce(array_keys($headers), function (RequestInterface $request, $header) use ($headers) {
            return $request->withHeader($header, $headers[$header]);
        }, $this->clientNegotiator->generateRequest($uri));

        if (!$request->getHeader('Origin')) {
            $request = $request->withHeader('Origin', str_replace('ws', 'http', $scheme) . '://' . $uri->getHost());
        }

        if (count($subProtocols) > 0) {
            $protocols = implode(',', $subProtocols);
            if ($protocols != "") {
                $request = $request->withHeader('Sec-WebSocket-Protocol', $protocols);
            }
        }

        return $request;
    }
}
