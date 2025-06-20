<?php

namespace App\Services;

use Exception;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class BotRabbitMQService
{
    private AMQPStreamConnection $connection;
    private AbstractChannel|AMQPChannel $channel;

    /**
     * @throws Exception
     */
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

    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }
}
