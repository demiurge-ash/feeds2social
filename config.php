<?php

return  [

    // main configuration
    [
        // mysql config
        'database' => 'feeds2social',
        'user' => 'root',
        'password' => '',
        'host' => 'localhost',

        // log-file
        'log_file' => __DIR__.'/log/feeds.log',
        // max filesize uploaded to the server, MB  (default 5MB)
        'upload_max_filesize' => 5,
        'upload_dir' => __DIR__.'/tmp/',
        // vkontake api version
        'vk_api_version' => '5.80',
        // vkontake image per post. it's recommended not more than 6
        'vk_image_per_post' => 6,
    ],

    // first feed's group configuration.
    // run: index.php?feed=1
    // you need to run the installation after adding a new group: index.php?mode=install
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
        'tm_token' => 'XXXXXXXXX:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
        // id of vkontake account. leave empty to disable
        'vk' =>  '-0000000000',
        // vkontake token
        'vk_token' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
        // group of feeds
        'feeds' => [
            'https://naked-science.ru/feedrss.xml',
            'https://habr.com/rss/interesting/',
            'https://tjournal.ru/rss/all',
        ]
    ],

    // second feed's group configuration.
    // run: index.php?feed=2
    // you need to run the installation after adding a new group: index.php?mode=install
    [
        // id of feed group [a-z0-9]
        'db' => 'news',
        // post main body of feed item [0-1]
        'message' => 1,
        // post title of feed item [0-1]
        'title' => 0,
        // post url of feed item [0-1]
        'link' => 0,
        // name of telegram channel. leave empty to disable
        'tm' => '@your_channel',
        // telegram token
        'tm_token' => 'XXXXXXXXX:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
        // id of vkontake account. leave empty to disable
        'vk' =>  '-0000000000',
        // vkontake token
        'vk_token' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
        // group of feeds
        'feeds' => [
            'http://rss.dw.de/xml/rss-ru-all',
            'https://iz.ru/xml/rss/all.xml',
            'http://rss.cnn.com/rss/edition.rss',
        ]
    ],

];
