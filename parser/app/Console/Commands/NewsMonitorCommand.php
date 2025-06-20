<?php

namespace App\Console\Commands;

use App\Services\NewsService;
use App\Services\ParserRabbitMQService;
use Exception;
use Illuminate\Console\Command;

class NewsMonitorCommand extends Command
{
    protected $signature = 'news:monitor
                            {--interval=10 : Interval between checks in seconds}
                            {--once : Run just once and exit}';

    protected $description = 'Monitor RSS feed for new news and publish to RabbitMQ';

    public function __construct(
        private readonly ParserRabbitMQService $rabbitMQService,
        private readonly NewsService     $newsService
    )
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $interval = (int)$this->option('interval');
        $runOnce = $this->option('once');

        $this->info("Starting news monitor. Checking every {$interval} seconds");

        do {
            try {
                $newItems = $this->newsService->checkFeed(config('services.news.rss_url'));

                foreach ($newItems as $newsItem) {
                    $this->rabbitMQService->publish('telegram', $newsItem->toJson());
                    $this->info("Published: " . $newsItem->title);
                }

                $this->info("Checked at: " . now() . " - Found " . count($newItems) . " new items");
            } catch (Exception $e) {
                $this->error("Error checking feed: " . $e->getMessage());
            }

            if (!$runOnce) {
                sleep($interval);
            }
        } while (!$runOnce);

        $this->rabbitMQService->close();
    }
}
