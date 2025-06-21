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

        $description = $this->limitToSentences($newsItem->description);

        $message = '📰 <b>'.htmlspecialchars($newsItem->title)."</b>\n";
        $message .= htmlspecialchars($description)."\n\n";
        $message .= "🔗 <a href='".htmlspecialchars($newsItem->link)."'>Читать на сайте</a>";
        $message .= "\n📂 <b>Категория:</b> ".htmlspecialchars($newsItem->category);
        $message .= "\n📅 <b>Дата публикации:</b> ".htmlspecialchars($pubDate);

        $telegram = new BotApi($botToken);

        $telegram->sendMessage(
            $channelId,
            $message,
            'HTML',
            true,
            null,
            null,
            false
        );
    }

    private function limitToSentences(?string $text, int $maxSentences = 3): string
    {
        if (empty($text)) {
            return '';
        }

        // Очищаем HTML теги и нормализуем пробелы
        $cleanText = trim(preg_replace('/\s+/', ' ', strip_tags($text)));

        if (empty($cleanText)) {
            return '';
        }

        // Убираем "Читать дальше" и его варианты, а также различные стрелочки
        $cleanText = preg_replace('/\s*(читать\s+дальше|читать\s+далее|подробнее|\-\>|\→|\➔|\➜|\➡|\➤|\➥|\➦|\➧|\➨|\➩|\➪|\➫|\➬|\➭|\➮|\➯|\➰|\➱|\⟶|\⟹|\⟼|\⟽|\⟾|\⟿|\⇀|\⇁|\⇂|\⇃|\⇄|\⇅|\⇆|\⇇|\⇈|\⇉|\⇊|\⇋|\⇌|\⇍|\⇎|\⇏|\⇐|\⇑|\⇒|\⇓|\⇔|\⇕|\⇖|\⇗|\⇘|\⇙|\⇚|\⇛|\⇜|\⇝|\⇞|\⇟|\⇠|\⇡|\⇢|\⇣|\⇤|\⇥|\⇦|\⇧|\⇨|\⇩|\⇪)\s*\.?\s*$/ui', '', $cleanText);

        // Разбиваем на предложения по точкам, восклицательным и вопросительным знакам
        $sentences = preg_split('/[.!?]+\s*/', $cleanText, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($sentences)) {
            return '';
        }

        // Берем максимум указанное количество предложений
        $limitedSentences = array_slice($sentences, 0, $maxSentences);

        // Соединяем предложения обратно
        $result = implode('. ', $limitedSentences);

        // Добавляем точку в конце, если её нет
        if (! preg_match('/[.!?]$/', $result)) {
            $result .= '.';
        }

        return $result;
    }
}
