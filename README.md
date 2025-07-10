# RSS Parser for Telegram News Channel | RSS-парсер для публикации новостей в Telegram

![Docker](https://img.shields.io/badge/Docker-2CA5E0?style=for-the-badge&logo=docker&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Telegram](https://img.shields.io/badge/Telegram-2CA5E0?style=for-the-badge&logo=telegram&logoColor=white)

## 🇬🇧 English Version

### 📝 Description
This is an RSS parser that automatically monitors news feeds and publishes them to your Telegram channel. The solution uses Docker for easy deployment and RabbitMQ for message queueing.

### � Before You Start
1. Create a Telegram bot via [BotFather](https://t.me/BotFather)
2. Add your bot to a public channel as an admin with post permissions

### 🚀 Installation & Setup

#### 1. Start Docker containers
```bash
docker-compose up -d
```

#### 2. Parser Setup (in `parser` folder)
```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan rabbitmq:init
php artisan news:monitor
```

#### 3. Bot Setup (in `bot` folder)
```bash
cp .env.example .env
# Edit .env with your credentials:
# TELEGRAM_BOT_TOKEN=your_bot_token
# TELEGRAM_CHANNEL_ID=@your_channel
php artisan news:consume
```

### ⚙️ Configuration
Edit `parser/config/config.php` to:
- Add new categories
- Add new RSS sources
- Configure translations

### 📜 License
MIT License - Free to use and modify

---

## 🇷🇺 Русская Версия

### 📝 Описание
RSS-парсер для автоматического мониторинга новостных лент и публикации новостей в ваш Telegram-канал. Решение использует Docker для простого развертывания и RabbitMQ для очереди сообщений.

### 🚧 Перед началом работы
1. Создайте бота через [BotFather](https://t.me/BotFather)
2. Добавьте бота в публичный канал как администратора с правами на публикацию

### � Установка и запуск

#### 1. Запуск Docker-контейнеров
```bash
docker-compose up -d
```

#### 2. Настройка парсера (в папке `parser`)
```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan rabbitmq:init
php artisan news:monitor
```

#### 3. Настройка бота (в папке `bot`)
```bash
cp .env.example .env
# Отредактируйте .env:
# TELEGRAM_BOT_TOKEN=ваш_токен
# TELEGRAM_CHANNEL_ID=@ваш_канал
php artisan news:consume
```

### ⚙️ Конфигурация
Измените `parser/config/config.php` для:
- Добавления новых категорий
- Добавления новых RSS-источников
- Настройки переводов

### 📜 Лицензия
MIT License - Свободное использование и модификация


[![GitHub](https://img.shields.io/badge/View_on_GitHub-181717?style=for-the-badge&logo=GitHub&logoColor=white)](https://github.com/gxldmane/rss-to-tg-parser)