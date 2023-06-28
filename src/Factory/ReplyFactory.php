<?php

declare(strict_types=1);

namespace Akuma\Centrifugo\Factory;

use Akuma\Centrifugo\Model\Reply;

class ReplyFactory implements ReplyFactoryInterface
{
    public function build(string $json): iterable
    {
        $data = explode("\n", $json);

        foreach ($data as $replyMessage) {
            $reply = json_decode($replyMessage, true);
            if (!$reply || empty($reply['id'])) {
                // Skipp non-reply messages
                continue;
            }
            $keys = array_keys($reply);
            $supportedReplies = [
                'connect' => Reply\ConnectReply::class,
                'history' => Reply\HistoryReply::class,
                'presence' => Reply\PresenceReply::class,
                'presence_stats' => Reply\PresenceStatsReply::class,
                'publish' => Reply\PublishReply::class,
                'refresh' => Reply\RefreshReply::class,
                'rpc' => Reply\RpcReply::class,
                'send' => Reply\SendReply::class,
                'sub_refresh' => Reply\SubRefreshReply::class,
                'subscribe' => Reply\SubscribeReply::class,
                'unsubscribe' => Reply\UnsubscribeReply::class,
                'error' => Reply\ErrorReply::class,
            ];
            foreach ($supportedReplies as $key => $class) {
                if (in_array($key, $keys)) {
                    yield call_user_func_array([$class, 'build'], [$replyMessage]);
                }
            }
        }
    }
}
