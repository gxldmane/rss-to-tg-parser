# RSS Parser for Telegram Channel | RSS Парсер для Telegram канала

![Docker](https://img.shields.io/badge/Docker-2CA5E0?style=for-the-badge&logo=docker&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Telegram](https://img.shields.io/badge/Telegram-2CA5E0?style=for-the-badge&logo=telegram&logoColor=white)

## 🌍 English Version

### 📝 Description
This is an RSS parser that automatically fetches news from RSS feeds and publishes them to your Telegram channel. The solution uses Docker for easy deployment and consists of two main components:
1. **Parser** - monitors RSS feeds and sends new items to a queue
2. **Bot** - consumes items from the queue and publishes them to Telegram

### 🚀 Getting Started

#### Prerequisites
1. Create a Telegram bot via [BotFather](https://t.me/BotFather)
2. Add your bot to a public channel as an admin (to allow posting)

#### Installation & Running
1. Run `docker-compose up` in the root directory
2. In the `parser` folder:
    - Rename `.env.example` to `.env`
    - Run `php artisan key:generate`
    - Run `php artisan migrate`
    - Initialize RabbitMQ: `php artisan rabbitmq:init`
    - Start the parser: `php artisan news:monitor`
3. In the `bot` folder:
    - Rename `.env.example` to `.env`
    - Configure `.env`:
        - `TELEGRAM_BOT_TOKEN` - your bot token from BotFather
        - `TELEGRAM_CHANNEL_ID` - ID of your channel where the bot was added
    - Start the bot: `php artisan news:consume`

## 🇷🇺 Русская версия

### 📝 Описание
RSS парсер для автоматической публикации новостей из RSS-лент в ваш Telegram канал. Решение использует Docker для простого развертывания и состоит из двух основных компонентов:
1. **Парсер** - отслеживает RSS-ленты и отправляет новые записи в очередь
2. **Бот** - забирает записи из очереди и публикует их в Telegram

### 🚀 Начало работы

#### Перед началом
1. Создайте бота в [BotFather](https://t.me/BotFather)
2. Добавьте бота в публичный канал как администратора (чтобы он мог публиковать посты)

#### Установка и запуск
1. Запустите `docker-compose up` в корневой папке
2. В папке `parser`:
    - Переименуйте `.env.example` в `.env`
    - Выполните `php artisan key:generate`
    - Выполните `php artisan migrate`
    - Инициализируйте RabbitMQ: `php artisan rabbitmq:init`
    - Запустите парсер: `php artisan news:monitor`
3. В папке `bot`:
    - Переименуйте `.env.example` в `.env`
    - Настройте `.env`:
        - `TELEGRAM_BOT_TOKEN` - токен вашего бота из BotFather
        - `TELEGRAM_CHANNEL_ID` - ID канала, куда добавлен бот
    - Запустите бота: `php artisan news:consume`

## 📜 License / Лицензия
MIT License. Developed by Timofey Khodotaev / Лицензия MIT. Разработано Тимофеем Ходотаевым

[![GitHub](https://img.shields.io/badge/View_on_GitHub-181717?style=for-the-badge&logo=GitHub&logoColor=white)](https://github.com/gxldmane/rss-to-tg-parser)