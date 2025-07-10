<?php

namespace App\Console\Commands;

use App\Services\NewsService;
use App\Services\ParserRabbitMQService;
use Exception;
use Illuminate\Console\Command;

class NewsMonitorCommand extends Command
{
    protected $signature = 'news:monitor
                            {--interval=60 : Interval between checks in seconds}
                            {--once : Run just once and exit}
                            {--categories=* : Categories to monitor (news, it, games)}';

    protected $description = 'Monitor RSS feed for new news and publish to RabbitMQ';

    public function __construct(
        private readonly ParserRabbitMQService $rabbitMQService,
        private readonly NewsService $newsService
    ) {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $interval = (int) $this->option('interval');
        $runOnce = $this->option('once');
        $selectedCategories = $this->option('categories');

        $allCategories = config('parser.rss_urls');
        $availableCategories = array_keys($allCategories);

        if (empty($selectedCategories)) {
            // Создаем массив с переведенными названиями
            $categoryChoices = [];
            foreach ($availableCategories as $category) {
                $translatedName = config('parser.translations.categories.'.$category, $category);
                $categoryChoices[] = $translatedName;
            }

            $selectedIndexes = $this->choice(
                'Выберите категории новостей для мониторинга',
                $categoryChoices,
                null,
                null,
                true
            );

            // Преобразуем выбранные переводы обратно в ключи категорий
            $selectedCategories = [];
            foreach ($selectedIndexes as $selectedTranslation) {
                $index = array_search($selectedTranslation, $categoryChoices);
                if ($index !== false) {
                    $selectedCategories[] = $availableCategories[$index];
                }
            }
        }

        $validCategories = array_intersect($selectedCategories, $availableCategories);
        $invalidCategories = array_diff($selectedCategories, $availableCategories);

        if (! empty($invalidCategories)) {
            $this->warn('Неизвестные категории: '.implode(', ', $invalidCategories));
        }

        if (empty($validCategories)) {
            $this->error('Не выбрано ни одной валидной категории!');

            return;
        }

        $selectedCategoryNames = array_map(function ($category) {
            return config('parser.translations.categories.'.$category, $category);
        }, $validCategories);

        $this->info('Выбранные категории: '.implode(', ', $selectedCategoryNames));
        $this->info("Starting news monitor. Checking every {$interval} seconds");

        do {
            try {
                $allNewItems = [];

                foreach ($validCategories as $category) {
                    $rssUrls = $allCategories[$category];

                    if (empty($rssUrls)) {
                        $this->warn("Категория '{$category}' не содержит RSS URL");

                        continue;
                    }

                    foreach ($rssUrls as $url) {
                        $translatedCategory = config('parser.translations.categories.'.$category, $category);
                        $rssConfig = ['url' => $url, 'category' => $translatedCategory];
                        $this->info("Checking feed: {$url} (category: {$translatedCategory})");

                        $newItems = $this->newsService->checkFeed($rssConfig);

                        foreach ($newItems as $newsItem) {
                            $allNewItems[] = $newsItem;
                        }

                        $this->info('Checked at: '.now().' - Found '.count($newItems).' new items');
                    }
                }

                if (! empty($allNewItems)) {
                    shuffle($allNewItems);

                    $this->info('Перемешано '.count($allNewItems).' новых элементов');

                    foreach ($allNewItems as $newsItem) {
                        $this->rabbitMQService->publish($newsItem->toJson());
                        $this->info("Published: {$newsItem->title} [{$newsItem->category}]");
                        usleep(100000); // 0.1 секунды
                    }
                }
            } catch (Exception $e) {
                $this->error('Error checking feed: '.$e->getMessage());
            }

            if (! $runOnce) {
                sleep($interval);
            }
        } while (! $runOnce);

        // Не закрываем соединение, чтобы очередь оставалась активной
        // $this->rabbitMQService->close();
    }
}
