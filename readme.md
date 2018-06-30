# Feeds to Social Media
<p><a href="./LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-blue.svg"></a></p>
Скрипт для автоматического ведения аккаунтов в социальных сетях с использованием групп RSS-лент.

На данный момент скрипт может работать с Vkontake и Telegram. Понимает протоколы RSS, Atom и RDF. 

### Как начать?

Скачайте репозиторий и установите его командой:
```bash
composer install
```

Настройте в файле config.php подключение к вашей базе MySQL:
```bash
        // mysql config
        'database' => 'feeds2social',
        'user' => 'root',
        'password' => '',
        'host' => 'localhost',
```

Настройте в этом же файле группу потоков.
Групп может быть сколько угодно. Не забывайте давать им уникальные имена.
Укажите, что нужно публиковать: текст новости (message), название новости (title) и ссылку на новость (link)
```bash
   [
        // id of feed group [a-z0-9]
        'db' => 'geeks',

        // post main body of feed item [0-1]
        'message' => 1,

        // post title of feed item [0-1]
        'title' => 1,

        // post url of feed item [0-1]
        'link' => 1,

        // name of telegram channel. leave empty to disable
        'tm' => '@your_channel',

        // telegram token
        'tm_token' => 'XXXXXXXX:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',

        // id of vkontake account. leave empty to disable
        'vk' =>  '-0000000000',

        // vkontake token
        'vk_token' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',

        // group of feeds
        'feeds' => [
            'https://naked-science.ru/feedrss.xml',
            'https://habr.com/rss/interesting/',
            'https://tjournal.ru/rss/all',
        ]
    ],
```

После добавления групп обязательно нужно провести их установку.
Для этого запустите  index.php?mode=install

Выставьте разрешение на запись каталогам /tmp и /log

Запуск работы группы: index.php?feed=1

Настройте запуск скрипта через cron, например раз в час.

### Как это работает?
Каждый запуск скрипт берет из следующего потока одну неопубликованную новость и отправляет в прикрепленные аккаунты.
Результат записывается в /log/feeds.log
