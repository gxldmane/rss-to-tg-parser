<?php

namespace App\Console\Commands;

use App\Actions\SendItemToTelegramAction;
use App\Data\NewsData;
use App\Services\BotRabbitMQService;
use Exception;
use Illuminate\Console\Command;

class ConsumeNewsCommand extends Command
{
    protected $signature = 'news:consume {--delay=10 : Delay in seconds between messages}';

    protected $description = 'Consume news from RabbitMQ and process them with delay';

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

        $delay = (int)$this->option('delay');

        $this->info("Starting news consumption with {$delay} seconds delay between messages");

        $callback = function ($msg) use ($delay) {
            try {
                $newsItem = NewsData::from(json_decode($msg->body, true, 512, JSON_THROW_ON_ERROR));

                SendItemToTelegramAction::run($newsItem);

                $this->info('Processed: ' . $newsItem->title);
                $this->info("Waiting {$delay} seconds before next message...");

                $msg->ack();

                sleep($delay);
            } catch (Exception $e) {
                $this->error('Error processing message: ' . $e->getMessage());
                $msg->nack();
            }
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume('telegram', '', false, false, false, false, $callback);

        $this->info('Waiting for messages. To exit press CTRL+C');

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
    }
}
