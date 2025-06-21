<?php

namespace App\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class NewsItemData extends Data
{
    public function __construct(
        public string  $title,
        public string  $link,
        public ?string  $description,
        public string  $category,
        public  $img = null,
        public Carbon  $pubDate,
    )
    {
    }

    public static function fromFeedItem($item, $rss): self
    {
        return new self(
            title: $item->get_title(),
            link: $item->get_link(),
            description: $item->get_description() ?? null,
            category: $rss['category'],
            img: $item->get_enclosure() ? $item->get_enclosure()->get_link() : null,
            pubDate: Carbon::parse($item->get_date()),
        );
    }
}
