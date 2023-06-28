<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Factory;

use Akuma\Centrifugo\Model\Push;

class PushFactory implements PushFactoryInterface
{
    public function build(string $json): iterable
    {
        $data = explode("\n", $json);

        foreach ($data as $replyMessage) {
            $reply = json_decode($replyMessage, true);
            if (!$reply || empty($reply['push'])) {
                // Skipp non-reply messages
                continue;
            }
            $keys = array_keys($reply);
            $supportedReplies = [
                'pub ' => Push\PubPush::class,
                'join' => Push\JoinPush::class,
                'leave' => Push\LeavePush::class,
                'unsubscribe' => Push\UnsubscribePush::class,
                'subscribe' => Push\SubscribePush::class,
                'disconnect' => Push\DisconnectPush::class,
                'message' => Push\MessagePush::class,
                'connect' => Push\ConnectPush::class,
                'refresh ' => Push\RefreshPush::class,
            ];
            foreach ($supportedReplies as $key => $class) {
                if (in_array($key, $keys)) {
                    yield call_user_func_array([$class, 'build'], [$replyMessage]);
                }
            }
        }
    }
}
