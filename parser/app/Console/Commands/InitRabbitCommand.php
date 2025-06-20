<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class InitRabbitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize RabbitMQ exchange and queue';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle(): void
    {
        $connection = new AMQPStreamConnection(
            Config::get('rabbitmq.host'),
            Config::get('rabbitmq.port'),
            Config::get('rabbitmq.user'),
            Config::get('rabbitmq.password'),
        );
        $channel = $connection->channel();

        $channel->exchange_declare('telegram', 'fanout', false, true, false);
        $channel->queue_declare('telegram', false, true, false);
        $channel->queue_bind('telegram', 'telegram');

        $this->info('RabbitMQ exchange and queue initialized successfully.');
    }
}
