<?php

namespace App\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class NewsData extends Data
{
    public function __construct(
        public string  $title,
        public string  $link,
        public ?string  $description,
        public string  $category,
        public $img = null,
        public Carbon  $pubDate,
    )
    {
    }
}
