<?php

namespace App\Services;

use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class BotRabbitMQService
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
    }

    public function consume(callable $callback): void
    {
        $this->channel->queue_declare('telegram', true, true, false, false);

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume('telegram', '', false, false, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
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
