<?php

namespace App\Actions;

use App\Data\NewsData;
use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\InvalidArgumentException;

class SendItemToTelegramAction
{
    use AsAction;

    /**
     * @throws \TelegramBot\Api\Exception
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function handle(NewsData $newsItem): void
    {
        $botToken = config('services.telegram.bot_token');
        $channelId = config('services.telegram.channel_id');

        $pubDate = $newsItem->pubDate->timezone('Europe/Moscow')->format('d.m.Y H:i');

        $description = trim(preg_replace('/\s+/', ' ', strip_tags($newsItem->description)));

        $message = "📰 <b>" . htmlspecialchars($newsItem->title) . "</b>\n";
        $message .= "🕒 <i>$pubDate</i>\n\n";
        $message .= htmlspecialchars(strip_tags($description)) . "\n\n";
        $message .= "🔗 <a href='" . htmlspecialchars($newsItem->link) . "'>Читать на сайте</a>";
        $message .= "\n#новости #актуальное";

        $telegram = new BotApi($botToken);

        $telegram->sendMessage(
            $channelId,
            $message,
            'HTML',
            false,
            null,
            null,
            true
        );
    }
}
