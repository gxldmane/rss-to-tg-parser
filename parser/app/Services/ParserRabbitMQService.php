<?php

namespace App\Services;

use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ParserRabbitMQService
{
    private AMQPStreamConnection $connection;

    private AbstractChannel|AMQPChannel $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password')
        );

        $this->channel = $this->connection->channel();

        // Убеждаемся, что очередь существует
        $this->ensureQueueExists();
    }

    private function ensureQueueExists(): void
    {
        $this->channel->exchange_declare('telegram', 'direct', false, true, false);
        $this->channel->queue_declare('telegram', false, true, false, false, false, [
            'x-message-ttl' => ['I', 3600000],
            'x-max-length' => ['I', 10000],
        ]);
        $this->channel->queue_bind('telegram', 'telegram', 'news');
    }

    public function publish(string $message): void
    {
        $msg = new AMQPMessage(
            $message,
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($msg, 'telegram', 'news');
    }

    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}
