<?php

namespace App\Console\Commands;

use App\Actions\SendItemToTelegramAction;
use App\Data\NewsData;
use App\Services\BotRabbitMQService;
use App\Services\RabbitMQService;
use Exception;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ConsumeNewsCommand extends Command
{
    protected $signature = 'news:consume';
    protected $description = 'Consume news from RabbitMQ and process them';

    public function __construct(
        private readonly BotRabbitMQService $rabbitMQService,
    )
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $channel = $this->rabbitMQService->getChannel();

        $callback = function ($msg) {
            try {
                $newsItem = NewsData::from(json_decode($msg->body, true, 512, JSON_THROW_ON_ERROR));

                SendItemToTelegramAction::run($newsItem);

                $this->info("Processed: " . $newsItem->title);
                $msg->ack();

            } catch (Exception $e) {
                $this->error("Error processing message: " . $e->getMessage());
                $msg->nack();
            }
        };

        $channel->basic_consume('telegram', '', false, false, false, false, $callback);

        $this->info('Waiting for messages. To exit press CTRL+C');

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
    }
}
