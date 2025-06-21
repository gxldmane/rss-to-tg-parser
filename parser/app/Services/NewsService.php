<?php

namespace App\Services;

use App\Data\NewsItemData;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Cache;
use Vedmant\FeedReader\Facades\FeedReader;

class NewsService
{
    /**
     * @throws BindingResolutionException
     */
    public function checkFeed(array $rss): array
    {
        $feed = FeedReader::read($rss['url']);
        $newItems = [];

        foreach ($feed->get_items() as $item) {
            $itemHash = $this->generateItemHash($item);

            if (!Cache::has("news_item:{$itemHash}")) {
                $newsItem = NewsItemData::fromFeedItem($item, $rss);
                $newItems[] = $newsItem;
                Cache::put("news_item:{$itemHash}", true, now()->addDays(1));
            }
        }

        return $newItems;
    }

    private function generateItemHash($item): string
    {
        return md5($item->get_link());
    }
}
