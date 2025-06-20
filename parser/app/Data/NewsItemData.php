<?php

namespace App\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class NewsItemData extends Data
{
    public function __construct(
        public string  $title,
        public string  $link,
        public string  $description,
        public Carbon  $pubDate,
    )
    {
    }

    public static function fromFeedItem($item): self
    {
        return new self(
            title: $item->get_title(),
            link: $item->get_link(),
            description: $item->get_description(),
            pubDate: Carbon::parse($item->get_date()),
        );
    }
}
